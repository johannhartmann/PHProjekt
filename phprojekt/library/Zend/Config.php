<?php
/**
 * Compatibility shim for Zend_Config
 */

/**
 * Base config class
 */
class Zend_Config implements ArrayAccess, Iterator, Countable
{
    /**
     * Configuration data
     * @var array
     */
    public $_data = [];

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
     */
    public function __construct($data = [], $allowModifications = false)
    {
        if (is_array($data)) {
            $this->_data = $data;
        }
        $this->_allowModifications = $allowModifications;
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
