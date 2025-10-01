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
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

/**
 * This class recive some fields, the data and one class for check,
 * and will validate each field deppend on the type and other restrictions.
 */
class Phprojekt_Model_Validate
{
    /**
     * Error object.
     *
     * @var Phprojekt_Error
     */
    public $error = null;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->error = new Phprojekt_Error();
    }

    /**
     * Define the clone function for prevent the same point to same object.
     *
     * @return void
     */
    public function __clone()
    {
        $this->error = new Phprojekt_Error();
    }

    /**
     * Return if the values are valid or not.
     *
     * @return boolean True for valid.
     */
    public function recordValidate(Phprojekt_ActiveRecord_Abstract $class, $data, $fields)
    {
        $valid = true;

        foreach ($data as $varname => $value) {
            if ($class->hasField($varname)) {
                // Validate with the database_manager stuff
                foreach ($fields as $field) {
                    if ($field['key'] == $varname) {

                        // Check required
                        if (true === $field['required']) {
                            $error = $this->validateIsRequired($value);
                            if (null !== $error) {
                                $valid = false;
                                $this->error->addError(array(
                                    'field'   => $varname,
                                    'label'   => $field['label'],
                                    'message' => $error));
                                break;
                            }
                        }

                        // Check value length
                        if (isset($field['length']) && $field['length'] > 0) {
                            if (strlen($value) > $field['length']) {
                                $valid = false;
                                $this->error->addError(array(
                                    'field'   => $varname,
                                    'label'   => $field['label'],
                                    'message' => Phprojekt::getInstance()->translate('Maximum length exceeded for '
                                        . 'field')));
                                break;
                            }
                        }

                        // Check value in range
                        if (($field['type'] == 'selectbox' || $field['type'] == 'multipleselectbox')
                        && !empty($value)) {
                            $found = false;
                            foreach ($field['range'] as $range) {
                                if ($range['id'] == $value) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $valid = false;
                                $this->error->addError(array(
                                    'field'   => $varname,
                                    'label'   => $field['label'],
                                    'message' => Phprojekt::getInstance()->translate('Value out of range')));
                                break;
                            }
                        } else if ($field['type'] == 'rating' && !empty($value)) {
                            if ($value > $field['range']['id'] || $value < 1) {
                                $valid = false;
                                $this->error->addError(array(
                                    'field'   => $varname,
                                    'label'   => $field['label'],
                                    'message' => Phprojekt::getInstance()->translate('Value out of range')));
                                break;
                            }
                        }

                        // Check value by type
                        $error = $this->validateValue($class, $varname, $value);
                        if (false === $error) {
                            $valid = false;
                            $this->error->addError(array(
                                'field'   => $varname,
                                'label'   => $field['label'],
                                'message' => Phprojekt::getInstance()->translate('Invalid Format')));
                        }
                        break;
                    }
                }

                // Validate an special fieldName
                $validator  = 'validate' . ucfirst($varname);
                if ($validator != 'validateIsRequired') {
                    if (method_exists($class, $validator)) {
                        $error = call_user_func(array($class, $validator), $value);
                        if (null !== $error) {
                            $valid = false;
                            $this->error->addError(array(
                                'field'   => $varname,
                                'label'   => $field['label'],
                                'message' => $error));
                        }
                    }
                }
            }
        }

        return $valid;
    }

    /**
     * Validates a value using the database type of the field.
     *
     * @param Phprojekt_Model_Interface $class   Model object.
     * @param string                    $varname Name of the field.
     * @param mix                       $value   Value to validate.
     *
     * @return boolean True for valid.
     */
    public function validateValue(Phprojekt_Model_Interface $class, $varname, $value)
    {
        $info       = $class->info();
        $varForInfo = Phprojekt_ActiveRecord_Abstract::convertVarToSql($varname);
        $valid      = true;
        if (isset($info['metadata'][$varForInfo]) && !empty($value)) {
            $type = $info['metadata'][$varForInfo]['DATA_TYPE'];
            switch ($type) {
                case 'int':
                    $valid = Cleaner::validate('integer', $value, false);
                    break;
                case 'float':
                    $valid = Cleaner::validate('float', $value, false);
                    break;
                case 'date':
                    $valid = Cleaner::validate('date', $value, false);
                    break;
                case 'time':
                    // $valid = Cleaner::validate('timestamp', $value, false);
                    break;
                case 'timestamp':
                case 'datetime';
                    $valid = Cleaner::validate('timestamp', $value, false);
                    break;
                default:
                    $valid = Cleaner::validate('string', $value, true);
                    break;
            }
        }

        return $valid !== false;
    }

    /**
     * Validates if a given value is required and returns an error message if it is empty.
     *
     * This method checks if the provided value is empty. If the value is empty, it returns
     * an error message indicating that the field is required. Otherwise, it returns null to
     * indicate that the value is valid.
     *
     * @param mixed $value The value to be validated as required.
     *
     * @return string|null An error message if the value is empty, or null if the value is not empty.
     */
    public function validateIsRequired($value)
    {
        $error = null;
        if (empty($value)) {
            $error = Phprojekt::getInstance()->translate('Is a required field');
        }

        return $error;
    }

    /**
     * Validates that the end date is not before the start date.
     *
     * This method takes two date strings, start and end, and checks that the end date is not
     * earlier than the start date. If the end date is earlier, an error is added to the error
     * object and the method returns false. Otherwise, it returns true.
     *
     * @param string $start The start date value to validate.
     * @param string $end   The end date value to validate.
     *
     * @return boolean True if the date range is valid, false otherwise.
     */
    public function validateDateRange($start, $end)
    {
        if (!empty($start) && !empty($end)) {
            if (strtotime($start) > strtotime($end)) {
                $this->error->addError(array(
                    'field'   => "Period",
                    'label'   => Phprojekt::getInstance()->translate('Period'),
                    'message' => Phprojekt::getInstance()->translate('End date can not be before Start date')));
                return false;
            }
        }

        return true;
    }
}
