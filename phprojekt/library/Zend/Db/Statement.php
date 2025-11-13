<?php
/**
 * Compatibility shim for Zend_Db_Statement
 * Wraps Laminas statement results for ZF1 compatibility
 */

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;

/**
 * Database statement wrapper for ZF1 compatibility
 */
class Zend_Db_Statement
{
    /**
     * Laminas result object
     * @var ResultInterface|StatementInterface
     */
    protected $_result;

    /**
     * Constructor
     *
     * @param ResultInterface|StatementInterface $result
     */
    public function __construct($result)
    {
        $this->_result = $result;
    }

    /**
     * Fetch all rows as array
     *
     * @param int $style Fetch style (ignored for compatibility)
     * @return array
     */
    public function fetchAll($style = null)
    {
        $rows = [];
        foreach ($this->_result as $row) {
            $rows[] = (array) $row;
        }
        return $rows;
    }

    /**
     * Fetch one row
     *
     * @param int $style Fetch style (ignored for compatibility)
     * @return array|false
     */
    public function fetch($style = null)
    {
        $row = $this->_result->current();
        if ($row) {
            $this->_result->next();
            return (array) $row;
        }
        return false;
    }

    /**
     * Fetch a column
     *
     * @param int $col Column number
     * @return mixed
     */
    public function fetchColumn($col = 0)
    {
        $row = $this->fetch();
        if ($row) {
            $values = array_values($row);
            return isset($values[$col]) ? $values[$col] : null;
        }
        return false;
    }

    /**
     * Fetch single value (first column of first row)
     *
     * @return mixed
     */
    public function fetchOne()
    {
        return $this->fetchColumn(0);
    }

    /**
     * Execute the statement
     *
     * @param array $params
     * @return bool
     */
    public function execute($params = null)
    {
        // Already executed in Laminas
        return true;
    }

    /**
     * Get row count
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->_result->count();
    }

    /**
     * Close cursor
     *
     * @return bool
     */
    public function closeCursor()
    {
        return true;
    }
}
