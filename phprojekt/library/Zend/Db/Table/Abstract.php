<?php
/**
 * Compatibility shim for Zend_Db_Table_Abstract
 * Wraps Laminas\Db\TableGateway for backward compatibility
 */

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;

abstract class Zend_Db_Table_Abstract
{
    /**
     * @var string Table name
     */
    protected $_name;

    /**
     * @var array Column information
     */
    protected $_colInfo = [];

    /**
     * @var array Row data
     */
    protected $_data = [];

    /**
     * @var Laminas\Db\Adapter\Adapter
     */
    protected $_db;

    /**
     * @var Laminas\Db\TableGateway\TableGateway
     */
    protected $_tableGateway;

    /**
     * @var string Primary key column name
     */
    protected $_primary = 'id';

    /**
     * Constructor
     *
     * @param mixed $config Configuration array or Zend_Db_Adapter
     */
    public function __construct($config = [])
    {
        // Get database adapter
        if (is_array($config) && isset($config['db'])) {
            $this->_db = $config['db'];
        } elseif ($config instanceof \Laminas\Db\Adapter\Adapter) {
            $this->_db = $config;
        } else {
            // Get from Phprojekt singleton
            if (class_exists('Phprojekt')) {
                $this->_db = Phprojekt::getInstance()->getDb();
            }
        }

        // Set up table name
        $this->_setupTableName();

        // Initialize table gateway
        if ($this->_name && $this->_db) {
            $this->_tableGateway = new TableGateway($this->_name, $this->_db);

            // Get column information
            try {
                $metadata = new \Laminas\Db\Metadata\Metadata($this->_db);
                $table = $metadata->getTable($this->_name);
                $columns = $table->getColumns();

                foreach ($columns as $column) {
                    $this->_colInfo[] = $column->getName();
                }
            } catch (\Exception $e) {
                // Column info not available - will be set by child class
            }
        }
    }

    /**
     * Set up table name based on class name
     */
    protected function _setupTableName()
    {
        if (empty($this->_name)) {
            // Convert ClassName to table_name
            $className = get_class($this);
            $parts = explode('_', $className);
            $this->_name = strtolower(end($parts));
        }
    }

    /**
     * Returns table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_name;
    }

    /**
     * Get table information
     *
     * @return array
     */
    public function info()
    {
        return [
            'name' => $this->_name,
            'cols' => $this->_colInfo,
            'primary' => $this->_primary,
        ];
    }

    /**
     * Get database adapter
     *
     * @return Laminas\Db\Adapter\Adapter
     */
    public function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Fetch all rows matching the criteria
     *
     * @param string|Select|Where $where
     * @return array|\Laminas\Db\ResultSet\ResultSet
     */
    public function fetchAll($where = null)
    {
        if (!$this->_tableGateway) {
            return [];
        }

        if ($where instanceof Select) {
            return $this->_tableGateway->selectWith($where);
        }

        return $this->_tableGateway->select($where);
    }

    /**
     * Fetch one row matching the criteria
     *
     * @param string|Select|Where $where
     * @return array|null
     */
    public function fetchRow($where = null)
    {
        $resultSet = $this->fetchAll($where);

        if ($resultSet instanceof \Laminas\Db\ResultSet\ResultSet) {
            return $resultSet->current();
        }

        return null;
    }

    /**
     * Find row(s) by primary key
     * Uses no type hint to match child class signatures
     *
     * @return array|\Laminas\Db\ResultSet\ResultSet
     */
    public function find()
    {
        $args = func_get_args();

        if (!$this->_tableGateway || empty($args)) {
            return [];
        }

        $id = $args[0];
        $where = [$this->_primary => $id];
        return $this->_tableGateway->select($where);
    }

    /**
     * Insert a new row
     *
     * @param array $data Column-value pairs
     * @return int Last insert ID
     */
    public function insert(array $data)
    {
        if (!$this->_tableGateway) {
            throw new \Exception('Table gateway not initialized');
        }

        $this->_tableGateway->insert($data);
        return $this->_db->getDriver()->getLastGeneratedValue();
    }

    /**
     * Update rows
     *
     * @param array $data Column-value pairs
     * @param string|array $where WHERE clause
     * @return int Number of affected rows
     */
    public function update(array $data, $where)
    {
        if (!$this->_tableGateway) {
            return 0;
        }

        return $this->_tableGateway->update($data, $where);
    }

    /**
     * Delete rows
     *
     * @param string|array $where WHERE clause
     * @return int Number of affected rows
     */
    public function delete($where)
    {
        if (!$this->_tableGateway) {
            return 0;
        }

        return $this->_tableGateway->delete($where);
    }

    /**
     * Create a new Select object
     *
     * @return Laminas\Db\Sql\Select
     */
    public function select()
    {
        $select = new Select();
        $select->from($this->_name);
        return $select;
    }

    /**
     * Get primary key column name
     *
     * @return string|array
     */
    public function getPrimaryKey()
    {
        return $this->_primary;
    }

    /**
     * Set default metadata cache (compatibility method - does nothing)
     *
     * @param mixed $cache
     */
    public static function setDefaultMetadataCache($cache)
    {
        // Laminas handles caching differently - this is a no-op for compatibility
    }

    /**
     * Get default metadata cache (compatibility method)
     *
     * @return object Mock cache object
     */
    public static function getDefaultMetadataCache()
    {
        // Return a mock cache object with clean() method
        return new class {
            public function clean() {
                // No-op
            }
        };
    }
}
