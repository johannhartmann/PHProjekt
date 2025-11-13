<?php
/**
 * Compatibility shim for Zend_Rest_Route
 */

class Zend_Rest_Route
{
    /**
     * Front controller
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * Constructor
     */
    public function __construct(Zend_Controller_Front $front)
    {
        $this->_front = $front;
    }

    /**
     * Match request
     */
    public function match($request)
    {
        // Simple REST route matching - stub implementation
        return false;
    }

    /**
     * Assemble URL
     */
    public function assemble($data = [], $reset = false, $encode = true)
    {
        return '';
    }
}
