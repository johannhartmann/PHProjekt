<?php
/**
 * Compatibility shim for Zend_Db_Select
 * Wraps Laminas\Db\Sql\Select for ZF1 compatibility
 */

use Laminas\Db\Sql\Select as LaminasSelect;
use Laminas\Db\Adapter\AdapterInterface;

/**
 * Database query builder for ZF1 compatibility
 */
class Zend_Db_Select extends LaminasSelect
{
    /**
     * Database adapter
     * @var AdapterInterface
     */
    protected $_adapter;

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->_adapter = $adapter;
        parent::__construct();
    }

    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Set columns to select
     * Matches parent signature for PHP 8 compatibility
     *
     * @param array $columns
     * @param bool $prefixColumnsWithTable
     * @return self
     */
    public function columns(array $columns, $prefixColumnsWithTable = true)
    {
        return parent::columns($columns, $prefixColumnsWithTable);
    }

    /**
     * __toString() - Returns the SQL for the select
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $sql = $this->getSqlString($this->_adapter->getPlatform());
            return $sql;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Assemble and return SQL string
     *
     * @return string
     */
    public function assemble()
    {
        return $this->__toString();
    }
}
