<?php
/**
 * Compatibility shim for Zend_Log
 */

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;
use Laminas\Log\Filter\FilterInterface;

class Zend_Log extends Logger
{
    /**
     * Whether default writer was added
     * @var bool
     */
    protected $_hasDefaultWriter = false;

    /**
     * Constructor
     */
    public function __construct($writer = null)
    {
        parent::__construct();

        if ($writer) {
            if ($writer instanceof Zend_Log_Writer_Abstract || $writer instanceof StreamWriter) {
                $this->addWriter($writer);
                $this->_hasDefaultWriter = false;
            }
        }
    }

    /**
     * Add writer
     */
    public function addWriter($writer, $priority = 1, $options = [])
    {
        $this->_hasDefaultWriter = false;
        return parent::addWriter($writer, $priority, $options);
    }

    /**
     * Ensure we have at least one writer
     */
    protected function _ensureWriter()
    {
        if (!$this->_hasDefaultWriter && !$this->getWriters()->count()) {
            try {
                parent::addWriter(new StreamWriter('php://memory'));
                $this->_hasDefaultWriter = true;
            } catch (\Exception $e) {
                // Silently fail
            }
        }
    }

    /**
     * Log a message
     */
    public function log($message, $priority, $extras = null)
    {
        $this->_ensureWriter();

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
class Zend_Log_Filter_Priority implements FilterInterface
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
     * Filter method (Laminas interface)
     */
    public function filter(array $event): bool
    {
        return $this->accept($event);
    }

    /**
     * Accept log message (Zend Framework compatibility)
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
