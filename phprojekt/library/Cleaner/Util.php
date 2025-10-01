<?php
/**
 * Util class.
 *
 * Some Utility Functions used by the Framework.
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * @category  PHProjekt
 * @package   Cleaner
 * @copyright Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license   LGPL v3 (See LICENSE file)
 * @link      http://www.thinkforge.org/projects/Cleaner
 * @since     File available since Release 6.0
 * @version   Release: 6.1.0
 * @author    Peter Voringer <peter.voringer@mayflower.de>
 */

/**
 * Util class.
 *
 * Some Utility Functions used by the Framework.
 *
 * @category  PHProjekt
 * @package   Cleaner
 * @copyright Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license   LGPL v3 (See LICENSE file)
 * @link      http://www.thinkforge.org/projects/Cleaner
 * @since     File available since Release 6.0
 * @version   Release: 6.1.0
 * @author    Peter Voringer <peter.voringer@mayflower.de>
 */
class Cleaner_Util
{
    /**
     * Checks if a given value is blank (null, empty string, or contains only whitespace characters).
     *
     * This method takes a mixed value as input and returns a boolean indicating whether the value is considered blank.
     * It first checks if the value is a string, and if not, returns false.
     * Then it trims any leading or trailing whitespace from the value and checks if the resulting string is empty.
     *
     * @param mixed $value The value to be tested for blankness
     * @return boolean True if the value is null, an empty string, or contains only whitespace characters; false otherwise
     */
    public static function isBlank($value)
    {
        if (!is_string($value) && !is_null($value)) {
            return false;
        }

        return trim($value) == '';
    }
}
