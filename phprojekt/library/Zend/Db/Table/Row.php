<?php
/**
 * Compatibility shim for Zend_Db_Table_Row
 */

class Zend_Db_Table_Row implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Table object
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Row data
     * @var array
     */
    protected $_data = [];

    /**
     * Modified columns
     * @var array
     */
    protected $_modifiedFields = [];

    /**
     * Constructor
     */
    public function __construct($config = [])
    {
        if (isset($config['table'])) {
            $this->_table = $config['table'];
        }
        if (isset($config['data'])) {
            $this->_data = $config['data'];
        }
    }

    /**
     * Get column value
     */
    public function __get($columnName)
    {
        return $this->_data[$columnName] ?? null;
    }

    /**
     * Set column value
     */
    public function __set($columnName, $value)
    {
        $this->_data[$columnName] = $value;
        $this->_modifiedFields[$columnName] = true;
    }

    /**
     * Check if column exists
     */
    public function __isset($columnName)
    {
        return isset($this->_data[$columnName]);
    }

    /**
     * Unset column
     */
    public function __unset($columnName)
    {
        unset($this->_data[$columnName]);
        $this->_modifiedFields[$columnName] = true;
    }

    /**
     * Get table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Get data as array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Save row
     */
    public function save()
    {
        if ($this->_table) {
            if (isset($this->_data['id']) && $this->_data['id']) {
                // Update
                $this->_table->update($this->_data, ['id = ?' => $this->_data['id']]);
            } else {
                // Insert
                $id = $this->_table->insert($this->_data);
                $this->_data['id'] = $id;
            }
            $this->_modifiedFields = [];
        }
        return $this;
    }

    /**
     * Delete row
     */
    public function delete()
    {
        if ($this->_table && isset($this->_data['id'])) {
            $this->_table->delete(['id = ?' => $this->_data['id']]);
        }
        return $this;
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
        $this->_data[$offset] = $value;
        $this->_modifiedFields[$offset] = true;
    }

    public function offsetUnset($offset): void
    {
        unset($this->_data[$offset]);
        $this->_modifiedFields[$offset] = true;
    }

    // IteratorAggregate implementation
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->_data);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->_data);
    }
}
