<?php
/**
 * Compatibility shim for Zend_Registry
 */

class Zend_Registry implements ArrayAccess, Iterator, Countable
{
    /**
     * Registry data
     * @var array
     */
    protected static $_registry = [];

    /**
     * Iterator position
     * @var int
     */
    protected static $_index = 0;

    /**
     * Set a registry value
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$_registry[$key] = $value;
    }

    /**
     * Get a registry value
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (!isset(self::$_registry[$key])) {
            throw new Zend_Registry_Exception("No entry is registered for key '$key'");
        }
        return self::$_registry[$key];
    }

    /**
     * Check if key exists
     *
     * @param string $key
     * @return bool
     */
    public static function isRegistered($key)
    {
        return isset(self::$_registry[$key]);
    }

    /**
     * Unset a registry value
     *
     * @param string $key
     */
    public static function _unsetInstance($key)
    {
        if (isset(self::$_registry[$key])) {
            unset(self::$_registry[$key]);
        }
    }

    /**
     * Get instance (for singleton pattern compatibility)
     */
    public static function getInstance()
    {
        return new self();
    }

    // ArrayAccess implementation
    public function offsetExists($offset): bool
    {
        return isset(self::$_registry[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return self::$_registry[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        self::$_registry[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset(self::$_registry[$offset]);
    }

    // Iterator implementation
    public function current(): mixed
    {
        $keys = array_keys(self::$_registry);
        $key = $keys[self::$_index] ?? null;
        return $key ? self::$_registry[$key] : null;
    }

    public function key(): mixed
    {
        $keys = array_keys(self::$_registry);
        return $keys[self::$_index] ?? null;
    }

    public function next(): void
    {
        self::$_index++;
    }

    public function rewind(): void
    {
        self::$_index = 0;
    }

    public function valid(): bool
    {
        $keys = array_keys(self::$_registry);
        return isset($keys[self::$_index]);
    }

    // Countable implementation
    public function count(): int
    {
        return count(self::$_registry);
    }
}

/**
 * Exception class
 */
class Zend_Registry_Exception extends Exception
{
}
