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

    /**
     * Add filter (for compatibility)
     */
    public function addFilter($filter)
    {
        // In Laminas, filters are added to writers, not the logger itself
        // For basic compatibility, we'll just ignore this or add it to all writers
        foreach ($this->getWriters() as $writer) {
            if (method_exists($writer, 'addFilter')) {
                $writer->addFilter($filter);
            }
        }
        return $this;
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
 * Priority filter
 */
class Zend_Log_Filter_Priority
{
    /**
     * @var int
     */
    protected $_priority;

    /**
     * @var string
     */
    protected $_operator;

    /**
     * Constructor
     */
    public function __construct($priority, $operator = '<=')
    {
        $this->_priority = $priority;
        $this->_operator = $operator;
    }

    /**
     * Accept log message
     */
    public function accept($event)
    {
        $priority = isset($event['priority']) ? $event['priority'] : 7;

        switch ($this->_operator) {
            case '<=':
                return $priority <= $this->_priority;
            case '<':
                return $priority < $this->_priority;
            case '>=':
                return $priority >= $this->_priority;
            case '>':
                return $priority > $this->_priority;
            case '==':
                return $priority == $this->_priority;
            case '!=':
                return $priority != $this->_priority;
            default:
                return true;
        }
    }
}

/**
 * Exception class
 */
class Zend_Log_Exception extends Exception
{
}
