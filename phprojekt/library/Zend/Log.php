<?php
/**
 * Compatibility shim for Zend_Log
 */

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;

class Zend_Log extends Logger
{
    /**
     * Constructor
     */
    public function __construct($writer = null)
    {
        parent::__construct();

        if ($writer) {
            if ($writer instanceof Zend_Log_Writer_Abstract) {
                $this->addWriter($writer);
            }
        }
    }

    /**
     * Log a message
     */
    public function log($message, $priority, $extras = null)
    {
        // Map Zend priority to PSR-3 level
        $levelMap = [
            0 => Logger::EMERG,
            1 => Logger::ALERT,
            2 => Logger::CRIT,
            3 => Logger::ERR,
            4 => Logger::WARN,
            5 => Logger::NOTICE,
            6 => Logger::INFO,
            7 => Logger::DEBUG,
        ];

        $level = $levelMap[$priority] ?? Logger::INFO;

        parent::log($level, $message, $extras ?? []);
    }
}

/**
 * Abstract log writer
 */
abstract class Zend_Log_Writer_Abstract
{
}

/**
 * Stream log writer
 */
class Zend_Log_Writer_Stream extends StreamWriter
{
    public function __construct($streamOrUrl, $mode = 'a')
    {
        parent::__construct($streamOrUrl);
    }
}

/**
 * Exception class
 */
class Zend_Log_Exception extends Exception
{
}
