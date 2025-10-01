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
 * Meta information about the Tab model.
 *
 * The fields are hardcore.
 */
class Phprojekt_Tab_Information extends Phprojekt_ModelInformation_Default
{
    /**
     * Sets a fields definitions for each field.
     *
     * @return void
     */
    /**
     * Sets the field definitions for the 'label' field..
     *
     * This method sets the field definition for the 'label' field, which is a required text field with a maximum length of 255 characters.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    /**
     * Sets the field definition for the 'label' field in the form..
     *
     * This method sets the field definition for the 'label' field, which is a required text field with a maximum length of 255 characters.
     * It is used to configure the form fields for the 'label' field.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    /**
     * Sets the field definition for the 'label' field in the form..
     *
     * This method sets the field definition for the 'label' field, which is a required text field with a maximum length of 255 characters.
     * It is used to configure the form fields for the 'label' field.
     * @return void This method does not return a value.
     * @note This method modifies global state.
     */
    public function setFields()
    {
        // label
        $this->fillField('label', 'Label', 'text', 1, 1, array(
            'required' => true,
            'length'   => 255));
    }
}
