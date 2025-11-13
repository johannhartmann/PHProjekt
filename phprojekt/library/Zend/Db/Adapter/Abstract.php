<?php
/**
 * Compatibility shim for Zend_Db_Adapter_Abstract
 * Wraps Laminas Db Adapter for backward compatibility
 */

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;

/**
 * Abstract database adapter for ZF1 compatibility
 * Wraps Laminas\Db\Adapter\Adapter
 */
class Zend_Db_Adapter_Abstract extends Adapter
{
    /**
     * Constructor
     *
     * @param array|AdapterInterface $config
     */
    public function __construct($config = [])
    {
        // If we receive an AdapterInterface (from Zend_Db::factory),
        // copy its configuration
        if ($config instanceof AdapterInterface) {
            $driver = $config->getDriver();
            $platform = $config->getPlatform();

            // Extract connection parameters from the existing adapter
            parent::__construct([
                'driver' => $driver,
                'platform' => $platform,
            ]);

            // Copy the driver connection
            $this->driver = $driver;
            $this->platform = $platform;
        } else {
            // Normal construction with config array
            parent::__construct($config);
        }
    }

    /**
     * Fetch all rows
     *
     * @param string|Laminas\Db\Sql\Select $sql
     * @return array
     */
    public function fetchAll($sql, $bind = [])
    {
        try {
            $result = $this->query($sql, $bind);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch one row
     *
     * @param string|Laminas\Db\Sql\Select $sql
     * @return array|null
     */
    public function fetchRow($sql, $bind = [])
    {
        try {
            $result = $this->query($sql, $bind);
            $row = $result->current();
            return $row ? (array) $row : null;
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch one column
     *
     * @param string $sql
     * @return array
     */
    public function fetchCol($sql, $bind = [])
    {
        try {
            $result = $this->query($sql, $bind);
            $rows = [];
            foreach ($result as $row) {
                $row = (array) $row;
                $rows[] = reset($row); // Get first column
            }
            return $rows;
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Fetch one value
     *
     * @param string $sql
     * @return mixed
     */
    public function fetchOne($sql, $bind = [])
    {
        try {
            $result = $this->query($sql, $bind);
            $row = $result->current();
            if ($row) {
                $row = (array) $row;
                return reset($row); // Get first column
            }
            return null;
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Quote identifier (table/column name)
     *
     * @param string $value
     * @return string
     */
    public function quoteIdentifier($value)
    {
        return $this->platform->quoteIdentifier($value);
    }

    /**
     * Quote value
     *
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        return $this->platform->quoteValue($value);
    }

    /**
     * Get server version
     *
     * @return string
     */
    public function getServerVersion()
    {
        try {
            $result = $this->query('SELECT VERSION()');
            $row = $result->current();
            return $row ? reset((array) $row) : 'unknown';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Close connection
     */
    public function closeConnection()
    {
        $this->driver->getConnection()->disconnect();
    }

    /**
     * List tables
     *
     * @return array
     */
    public function listTables()
    {
        try {
            $sql = 'SHOW TABLES';
            $result = $this->query($sql);
            $tables = [];
            foreach ($result as $row) {
                $row = (array) $row;
                $tables[] = reset($row);
            }
            return $tables;
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Describe table
     *
     * @param string $tableName
     * @return array
     */
    public function describeTable($tableName)
    {
        try {
            $sql = 'DESCRIBE ' . $this->quoteIdentifier($tableName);
            $result = $this->query($sql);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new Zend_Db_Adapter_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get last insert ID
     *
     * @param string $tableName
     * @param string $primaryKey
     * @return int
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return $this->driver->getLastGeneratedValue($primaryKey);
    }

    /**
     * Create a SELECT query builder
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        return new Zend_Db_Select($this);
    }

    /**
     * Execute a query
     * Overrides parent to properly handle Zend_Db_Select objects
     *
     * @param string|Zend_Db_Select $sql
     * @param string|array $parametersOrQueryMode
     * @param \Laminas\Db\ResultSet\ResultSetInterface|null $resultPrototype
     * @return Zend_Db_Statement
     */
    public function query($sql, $parametersOrQueryMode = self::QUERY_MODE_PREPARE, ?\Laminas\Db\ResultSet\ResultSetInterface $resultPrototype = null)
    {
        // Convert Zend_Db_Select to SQL string
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->getSqlString($this->platform);
        }

        // Call parent query method
        $result = parent::query($sql, $parametersOrQueryMode, $resultPrototype);

        // Wrap in Zend_Db_Statement for ZF1 compatibility
        return new Zend_Db_Statement($result);
    }
}
