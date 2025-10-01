<?php
/**
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * @copyright  Copyright (c) 2012 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

/**
 * Exception thrown when trying to save an entry that overlaps
 */
class Phprojekt_Exception_InvalidParameter extends Phprojekt_Exception_Published
{
    protected $_type     = 'invalid_parameter';
    protected $_httpCode = 422;

    protected $_field;
    protected $_value;
    protected $_shouldMatch;

    function __construct($field, $value, $shouldMatch = null)
    {
        $message = "Invalid value \"$value\" for $field";
        if (!is_null($shouldMatch)) {
            $message .= ", it should match $shouldMatch";
        }
        parent::__construct($message);

        $this->_field       = $field;
        $this->_value       = $value;
        $this->_shouldMatch = $shouldMatch;
    }

    /**
     * Converts the InvalidParameter exception object into an associative array representation.
     *
     * This method returns an associative array containing the exception message, the invalid
     * field name, the invalid value, and whether the value should have matched a specific pattern.
     *
     * @return array An associative array containing the exception details.
     */
    public function toArray()
    {
        $ret = parent::toArray();

        $ret['field']       = $this->_field;
        $ret['value']       = $this->_value;
        $ret['shouldMatch'] = $this->_shouldMatch;

        return $ret;
    }
}
