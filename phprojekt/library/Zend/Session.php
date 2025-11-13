<?php
/**
 * Compatibility shim for Zend_Session
 */

use Laminas\Session\SessionManager;
use Laminas\Session\Container;

class Zend_Session
{
    /**
     * Session manager
     * @var SessionManager
     */
    protected static $_sessionManager;

    /**
     * Get session manager
     */
    public static function getSessionManager()
    {
        if (!self::$_sessionManager) {
            self::start();
        }
        return self::$_sessionManager;
    }

    /**
     * Start session
     */
    public static function start()
    {
        if (!self::$_sessionManager) {
            self::$_sessionManager = new SessionManager();
        }

        try {
            self::$_sessionManager->start();
        } catch (\Exception $e) {
            throw new Zend_Session_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Write and close session
     */
    public static function writeClose()
    {
        if (self::$_sessionManager) {
            self::$_sessionManager->writeClose();
        }
    }

    /**
     * Regenerate session ID
     */
    public static function regenerateId()
    {
        if (!self::$_sessionManager) {
            self::start();
        }
        self::$_sessionManager->regenerateId();
    }

    /**
     * Destroy session
     */
    public static function destroy()
    {
        if (self::$_sessionManager) {
            self::$_sessionManager->destroy();
        }
    }

    /**
     * Check if session exists
     */
    public static function sessionExists()
    {
        return self::$_sessionManager && self::$_sessionManager->sessionExists();
    }

    /**
     * Get session ID
     */
    public static function getId()
    {
        if (!self::$_sessionManager) {
            self::start();
        }
        return self::$_sessionManager->getId();
    }

    /**
     * Set session ID
     */
    public static function setId($id)
    {
        if (!self::$_sessionManager) {
            self::$_sessionManager = new SessionManager();
        }
        self::$_sessionManager->setId($id);
    }
}

/**
 * Session namespace container
 */
class Zend_Session_Namespace extends Container
{
    /**
     * Constructor
     */
    public function __construct($namespace = 'Default', $manager = null)
    {
        if (!$manager) {
            $manager = Zend_Session::getSessionManager();
        }

        // Sanitize namespace for Laminas - only allow alphanumerics, backslashes, and underscores
        $namespace = preg_replace('/[^a-zA-Z0-9_\\\\]/', '_', $namespace);

        parent::__construct($namespace, $manager);
    }
}

/**
 * Exception class
 */
class Zend_Session_Exception extends Exception
{
}
