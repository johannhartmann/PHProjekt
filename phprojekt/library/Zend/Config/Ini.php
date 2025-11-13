<?php
/**
 * Compatibility shim for Zend_Config_Ini
 */

use Laminas\Config\Reader\Ini as IniReader;
use Laminas\Config\Config as LaminasConfig;

class Zend_Config_Ini implements ArrayAccess, Iterator, Countable
{
    /**
     * Configuration data
     * @var array
     */
    protected $_data = [];

    /**
     * Current section
     * @var string
     */
    protected $_section;

    /**
     * Allow modifications
     * @var bool
     */
    protected $_allowModifications = false;

    /**
     * Current iterator index
     * @var int
     */
    protected $_index = 0;

    /**
     * Constructor
     *
     * @param string $filename INI file to load
     * @param string $section Section to load
     * @param bool $allowModifications Allow modifications
     */
    public function __construct($filename, $section = null, $allowModifications = false)
    {
        $this->_section = $section;
        $this->_allowModifications = $allowModifications;

        if (!file_exists($filename)) {
            throw new Zend_Config_Exception("File '$filename' doesn't exist");
        }

        try {
            $reader = new IniReader();
            $config = $reader->fromFile($filename);

            if ($section !== null) {
                if (!isset($config[$section])) {
                    throw new Zend_Config_Exception("Section '$section' not found in INI file");
                }
                $this->_data = $config[$section];
            } else {
                $this->_data = $config;
            }

            // Convert nested arrays to Zend_Config objects
            $this->_data = $this->_convertToConfig($this->_data);
        } catch (\Exception $e) {
            throw new Zend_Config_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Convert array to nested Zend_Config objects
     */
    protected function _convertToConfig($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $config = new static('', null, $this->_allowModifications);
                $config->_data = $this->_convertToConfig($value);
                $data[$key] = $config;
            }
        }

        return $data;
    }

    /**
     * Magic getter
     */
    public function __get($name)
    {
        return $this->_data[$name] ?? null;
    }

    /**
     * Magic setter
     */
    public function __set($name, $value)
    {
        if (!$this->_allowModifications) {
            throw new Zend_Config_Exception('Config is read-only');
        }
        $this->_data[$name] = $value;
    }

    /**
     * Magic isset
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Magic unset
     */
    public function __unset($name)
    {
        if (!$this->_allowModifications) {
            throw new Zend_Config_Exception('Config is read-only');
        }
        unset($this->_data[$name]);
    }

    /**
     * Get as array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->_data as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    // ArrayAccess implementation
    public function offsetExists($offset): bool
    {
        return isset($this->_data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->_data[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (!$this->_allowModifications) {
            throw new Zend_Config_Exception('Config is read-only');
        }
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        if (!$this->_allowModifications) {
            throw new Zend_Config_Exception('Config is read-only');
        }
        unset($this->_data[$offset]);
    }

    // Iterator implementation
    public function current(): mixed
    {
        $keys = array_keys($this->_data);
        return $this->_data[$keys[$this->_index]] ?? null;
    }

    public function key(): mixed
    {
        $keys = array_keys($this->_data);
        return $keys[$this->_index] ?? null;
    }

    public function next(): void
    {
        $this->_index++;
    }

    public function rewind(): void
    {
        $this->_index = 0;
    }

    public function valid(): bool
    {
        $keys = array_keys($this->_data);
        return isset($keys[$this->_index]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->_data);
    }
}

/**
 * Exception class
 */
class Zend_Config_Exception extends Exception
{
}
