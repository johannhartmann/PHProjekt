<?php
/**
 * Compatibility shim for Zend_Controller_Front
 */

class Zend_Controller_Front
{
    /**
     * Singleton instance
     * @var Zend_Controller_Front
     */
    protected static $_instance;

    /**
     * Request object
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Base URL
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get request
     */
    public function getRequest()
    {
        if ($this->_request === null) {
            $this->_request = new Zend_Controller_Request_Http();
        }
        return $this->_request;
    }

    /**
     * Set request
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Set base URL
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }
}
