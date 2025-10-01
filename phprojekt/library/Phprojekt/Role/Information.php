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
* Meta information about the Role model.
 *
 * The fields are hardcore.
 */
class Phprojekt_Role_Information extends Phprojekt_ModelInformation_Default
{
    /**
     * Sets a fields definitions for each field.
     *
     * @return void
     */
    /**
     * Sets the field definitions for the 'name' field..
     *
     * This method sets up the field definition for the 'name' field, including the field label, type, and various validation rules such as being required and having a maximum length of 255 characters.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    /**
     * Sets the field definitions for the 'name' field..
     *
     * This method sets up the field definition for the 'name' field, including the field label, type, and various validation rules such as being required and having a maximum length of 255 characters.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    /**
     * Sets the field definition for the 'name' field in the role information..
     *
     * This method sets up the field definition for the 'name' field, including the field label, type, and various validation rules such as being required and having a maximum length of 255 characters.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    public function setFields()
    {
        // name
        $this->fillField('name', 'Name', 'text', 1, 1, array(
            'required' => true,
            'length'   => 255));
    }
}
