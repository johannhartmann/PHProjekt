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
 * A generic interface to interact with ModelInformation.
 */
interface Phprojekt_ModelInformation_Interface
{
    /**
     * Return an array of field definitions.
     *
     * @return array Array with all the fields definitions.
     */
     public function getFieldDefinition();

    /**
     * Return the type of one field.
     *
     * @param string $fieldName The name of the field to check.
     *
     * @return string Type of the field.
     */
    /**
     * Returns the type of a specified field.
     *
     * This method retrieves the data type of a field in the model.
     * It is part of the ModelInformation interface, which provides information about the structure and properties of a data model.
     *
     * @param string $fieldName The name of the field to retrieve the type for
     * @return string The data type of the specified field
     */
    /**
     * Retrieves the data type of a specified field in the model..
     *
     * This method is part of the ModelInformation interface, which provides information about the structure and properties of a data model.
     * It allows you to get the data type of a field in the model by providing the field name.
     *
     * @param string $fieldName The name of the field to retrieve the type for.
     * @return string The data type of the specified field.
     */
    /**
     * Retrieves the data type of a specified field in the model..
     *
     * This method is part of the ModelInformation interface, which provides information about the structure and properties of a data model.
     * It allows you to get the data type of a field in the model by providing the field name.
     *
     * @param string $fieldName The name of the field to retrieve the type for.
     * @return string The data type of the specified field.
     */
    public function getType($fieldName);
}
