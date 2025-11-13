<?php
/**
 * Compatibility shim for Zend_Auth
 */

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session as SessionStorage;

class Zend_Auth extends AuthenticationService
{
    /**
     * Singleton instance
     * @var Zend_Auth
     */
    protected static $_instance;

    /**
     * Constructor
     */
    public function __construct($storage = null, $adapter = null)
    {
        if ($storage === null) {
            $storage = new SessionStorage();
        }

        parent::__construct($storage, $adapter);
    }

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
     * Authenticate with adapter
     */
    public function authenticate($adapter = null)
    {
        if ($adapter !== null) {
            $this->setAdapter($adapter);
        }

        return parent::authenticate();
    }

    /**
     * Check if has identity
     */
    public function hasIdentity()
    {
        return parent::hasIdentity();
    }

    /**
     * Get identity
     */
    public function getIdentity()
    {
        return parent::getIdentity();
    }

    /**
     * Clear identity
     */
    public function clearIdentity()
    {
        parent::clearIdentity();
    }

    /**
     * Get storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }
}

/**
 * Auth adapter interface
 */
interface Zend_Auth_Adapter_Interface
{
    public function authenticate();
}

/**
 * Auth result
 */
class Zend_Auth_Result extends \Laminas\Authentication\Result
{
    const SUCCESS = 1;
    const FAILURE = 0;
    const FAILURE_IDENTITY_NOT_FOUND = -1;
    const FAILURE_IDENTITY_AMBIGUOUS = -2;
    const FAILURE_CREDENTIAL_INVALID = -3;
    const FAILURE_UNCATEGORIZED = -4;

    public function __construct($code, $identity, $messages = [])
    {
        parent::__construct($code, $identity, $messages);
    }

    public function isValid()
    {
        return $this->code > 0;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
