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
 * Clean an string for index it.
 */
class Phprojekt_Converter_String
{
    /**
     * Clean Up a string for index.
     *
     * @param string $string The string for cleanup.
     *
     * @return string Cleaned string.
     */
    public static function cleanupString($string)
    {
        // Clean up HTML
        $string = strip_tags($string);
        $string = mb_strtolower($string, 'UTF-8');

        return $string;
    }

    /**
     * Checks if a string has a length between 3 and 256 characters.
     *
     * This method takes a string as input and returns a boolean value indicating
     * whether the length of the string is between 3 and 256 characters (inclusive).
     * It uses the mb_strlen() function to determine the length of the string in UTF-8 encoding.
     *
     * @param string $string The input string to be checked for length.
     *
     * @return boolean True if the length is between 3 and 256 characters, false otherwise.
     */
    public static function stripLengthWords($string)
    {
        $len = mb_strlen($string, 'UTF-8');

        return ($len > 2 && $len < 256);
    }
}
