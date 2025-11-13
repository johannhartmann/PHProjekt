<?php
/**
 * Compatibility shim for Zend_Db_Table_Abstract
 *
 * This provides backward compatibility for code using Zend_Db_Table_Abstract
 * by wrapping Laminas\Db\TableGateway functionality.
 */

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Cache\Storage\StorageInterface;

abstract class Zend_Db_Table_Abstract extends AbstractTableGateway
{
    /**
     * Table name
     * @var string
     */
    protected $_name;

    /**
     * Primary key column(s)
     * @var string|array
     */
    protected $_primary;

    /**
     * Reference map for foreign keys
     * @var array
     */
    protected $_referenceMap = [];

    /**
     * Dependent tables
     * @var array
     */
    protected $_dependentTables = [];

    /**
     * Metadata cache (stores Zend_Cache_Core wrapper)
     * @var Zend_Cache_Core
     */
    protected static $_metadataCache;

    /**
     * Default database adapter
     * @var AdapterInterface
     */
    protected static $_defaultAdapter;

    /**
     * Constructor
     */
    public function __construct($config = [])
    {
        // Set table name from $_name if not in config
        if (!isset($config['table']) && isset($this->_name)) {
            $this->table = $this->_name;
        } elseif (isset($config['table'])) {
            $this->table = $config['table'];
            $this->_name = $config['table'];
        }

        // Set adapter
        if (isset($config['adapter'])) {
            $this->adapter = $config['adapter'];
        } elseif (self::$_defaultAdapter) {
            $this->adapter = self::$_defaultAdapter;
        }

        // Initialize resultSetPrototype if not set
        if (!$this->resultSetPrototype) {
            $this->resultSetPrototype = new ResultSet();
        }

        // Call parent if adapter is set
        if ($this->adapter) {
            parent::initialize();
        }
    }

    /**
     * Get table name
     * @return string
     */
    public function info($key = null)
    {
        $info = [
            'name' => $this->_name ?? $this->table,
            'cols' => [], // Would need to query database for column info
            'primary' => $this->_primary,
            'metadata' => [],
        ];

        if ($key) {
            return $info[$key] ?? null;
        }
        return $info;
    }

    /**
     * Set default adapter
     */
    public static function setDefaultAdapter(AdapterInterface $adapter)
    {
        self::$_defaultAdapter = $adapter;
    }

    /**
     * Get default adapter
     */
    public static function getDefaultAdapter()
    {
        return self::$_defaultAdapter;
    }

    /**
     * Set default metadata cache
     */
    public static function setDefaultMetadataCache($cache)
    {
        // Accept both Zend_Cache_Core and StorageInterface for compatibility
        if ($cache instanceof Zend_Cache_Core) {
            // Store the Zend_Cache_Core wrapper directly
            self::$_metadataCache = $cache;
        } else if ($cache instanceof StorageInterface) {
            // Wrap Laminas storage in Zend_Cache_Core for compatibility
            self::$_metadataCache = new Zend_Cache_Core($cache);
        } else {
            throw new \InvalidArgumentException('Cache must be instance of Zend_Cache_Core or StorageInterface');
        }
    }

    /**
     * Get default metadata cache
     * Always returns Zend_Cache_Core for ZF1 compatibility
     */
    public static function getDefaultMetadataCache()
    {
        if (!self::$_metadataCache) {
            // Create a dummy no-op storage adapter
            $dummyStorage = new class implements StorageInterface {
                public function setOptions($options) {}
                public function getOptions() { return new \stdClass(); }
                public function setItem($key, $value) { return true; }
                public function setItems(array $keyValuePairs) { return []; }
                public function addItem($key, $value) { return true; }
                public function addItems(array $keyValuePairs) { return []; }
                public function replaceItem($key, $value) { return true; }
                public function replaceItems(array $keyValuePairs) { return []; }
                public function getItem($key, &$success = null, &$casToken = null) { $success = false; return null; }
                public function getItems(array $keys) { return []; }
                public function hasItem($key) { return false; }
                public function hasItems(array $keys) { return []; }
                public function getMetadata($key) { return null; }
                public function getMetadatas(array $keys) { return []; }
                public function removeItem($key) { return true; }
                public function removeItems(array $keys) { return []; }
                public function checkAndSetItem($token, $key, $value) { return true; }
                public function touchItem($key) { return true; }
                public function touchItems(array $keys) { return []; }
                public function incrementItem($key, $value) { return 1; }
                public function incrementItems(array $keyValuePairs) { return []; }
                public function decrementItem($key, $value) { return 1; }
                public function decrementItems(array $keyValuePairs) { return []; }
                public function getCapabilities() { return new \Laminas\Cache\Storage\Capabilities($this, new \stdClass()); }
                public function clearByNamespace($namespace) { return true; }
                public function clearExpired() { return true; }
                public function flush() { return true; }
            };

            // Wrap in Zend_Cache_Core
            self::$_metadataCache = new Zend_Cache_Core($dummyStorage);
        }
        return self::$_metadataCache;
    }

    /**
     * Get adapter
     */
    public function getAdapter()
    {
        return $this->adapter ?? self::$_defaultAdapter;
    }

    /**
     * Get table name
     */
    public function getTableName()
    {
        return $this->_name ?? $this->table;
    }

    /**
     * Fetch all rows (compatibility method)
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        return $this->select(function($select) use ($where, $order, $count, $offset) {
            if ($where) {
                if (is_string($where)) {
                    $select->where($where);
                } elseif (is_array($where)) {
                    foreach ($where as $key => $value) {
                        $select->where([$key => $value]);
                    }
                }
            }
            if ($order) {
                $select->order($order);
            }
            if ($count !== null) {
                $select->limit($count);
            }
            if ($offset !== null) {
                $select->offset($offset);
            }
        });
    }

    /**
     * Fetch row (compatibility method)
     */
    public function fetchRow($where = null, $order = null)
    {
        $rowset = $this->fetchAll($where, $order, 1);
        return $rowset->current();
    }

    /**
     * Create row (compatibility method)
     */
    public function createRow(array $data = [], $defaultSource = null)
    {
        $row = new Zend_Db_Table_Row(['table' => $this, 'data' => $data]);
        return $row;
    }

    /**
     * Find by primary key
     * Uses func_get_args() for compatibility with child classes
     */
    public function find()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('Missing argument');
        }

        $id = $args[0];

        if (!$this->_primary) {
            throw new Exception('No primary key defined');
        }

        $where = [$this->_primary => $id];
        return $this->fetchAll($where);
    }
}
