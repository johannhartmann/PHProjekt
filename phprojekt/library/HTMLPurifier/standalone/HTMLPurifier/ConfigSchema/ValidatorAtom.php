<?php

/**
 * Fluent interface for validating the contents of member variables.
 * This should be immutable. See HTMLPurifier_ConfigSchema_Validator for
 * use-cases. We name this an 'atom' because it's ONLY for validations that
 * are independent and usually scalar.
 */
class HTMLPurifier_ConfigSchema_ValidatorAtom
{
    
    protected $context, $obj, $member, $contents;
    
    public function __construct($context, $obj, $member) {
        $this->context     = $context;
        $this->obj         = $obj;
        $this->member      = $member;
        $this->contents    =& $obj->$member;
    }
    
    public function assertIsString() {
        if (!is_string($this->contents)) $this->error('must be a string');
        return $this;
    }
    
    public function assertIsBool() {
        if (!is_bool($this->contents)) $this->error('must be a boolean');
        return $this;
    }
    
    public function assertIsArray() {
        if (!is_array($this->contents)) $this->error('must be an array');
        return $this;
    }
    
    public function assertNotNull() {
        if ($this->contents === null) $this->error('must not be null');
        return $this;
    }
    
    public function assertAlnum() {
        $this->assertIsString();
        if (!ctype_alnum($this->contents)) $this->error('must be alphanumeric');
        return $this;
    }
    
    public function assertNotEmpty() {
        if (empty($this->contents)) $this->error('must not be empty');
        return $this;
    }
    
    public function assertIsLookup() {
        $this->assertIsArray();
        foreach ($this->contents as $v) {
            if ($v !== true) $this->error('must be a lookup array');
        }
        return $this;
    }
    
    /**
     * Throws a configuration schema exception with a formatted error message..
     *
     * This protected method is used internally to throw a new HTMLPurifier_ConfigSchema_Exception with a formatted error message.
     * The error message includes the name of the configuration directive member and the context in which the error occurred.
     *
     * @param string $msg The error message to include in the exception.
     * @throws HTMLPurifier_ConfigSchema_Exception Whenever this method is called.
     * @note This method modifies global state.
     */
    /**
     * Throws a configuration schema exception with a formatted error message..
     *
     * This protected method is used internally to throw a new HTMLPurifier_ConfigSchema_Exception with a formatted error message.
     * The error message includes the name of the configuration directive member and the context in which the error occurred.
     *
     * @param string $msg The error message to include in the exception.
     * @throws HTMLPurifier_ConfigSchema_Exception Whenever this method is called.
     * @note This method modifies global state.
     */
    /**
     * Throws a configuration schema exception with a formatted error message..
     *
     * This protected method is used internally to throw a new HTMLPurifier_ConfigSchema_Exception with a formatted error message.
     * The error message includes the name of the configuration directive member and the context in which the error occurred.
     *
     * @param string $msg The error message to include in the exception.
     * @throws HTMLPurifier_ConfigSchema_Exception Whenever this method is called.
     * @note This method modifies global state.
     */
    protected function error($msg) {
        throw new HTMLPurifier_ConfigSchema_Exception(ucfirst($this->member) . ' in ' . $this->context . ' ' . $msg);
    }
    
}


