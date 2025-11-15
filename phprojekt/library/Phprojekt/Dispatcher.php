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

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Phprojekt own dispatcher.
 */
class Phprojekt_Dispatcher extends AbstractPlugin
{
    /**
     * Formats a string from a URI into a PHP-friendly name.
     *
     * By default, replaces words separated by the word separator character(s) with camelCaps.
     * If $isAction is false, it also preserves replaces words separated by the path
     * separation character with an underscore, making the following word Title cased.
     * All non-alphanumeric characters are removed.
     * The function works with calls like jsonSearch instead of jsonsearch.
     *
     * @param string  $unformatted String.
     * @param boolean $isAction    Defaults to false.
     *
     * @return string
     */
    /**
     * Formats a string into a PHP-friendly name..
     *
     * This method takes an unformatted string and formats it into a PHP-friendly name.
     * By default, it replaces words separated by the word separator character(s) with camelCaps.
     * If `$isAction` is false, it also preserves words separated by the path separation character with an underscore, making the following word Title cased.
     * All non-alphanumeric characters are removed.
     *
     * @param string $unformatted The unformatted string to be formatted.
     * @param boolean $isAction Defaults to false. If true, the method will not preserve words separated by the path separation character with an underscore.
     * @return string The formatted string.
     */
    /**
     * Formats a string into a PHP-friendly name.
     *
     * This method takes an unformatted string and formats it into a PHP-friendly name.
     * By default, it replaces words separated by the word separator character(s) with camelCaps.
     * If `$isAction` is false, it also preserves words separated by the path separation character with an underscore, making the following word Title cased.
     * All non-alphanumeric characters are removed.
     *
     * @param string $unformatted The unformatted string to be formatted
     * @param boolean $isAction Defaults to false. If true, the method will not preserve words separated by the path separation character with an underscore
     * @return string The formatted string
     */
    /**
     * Formats a string into a PHP-friendly name..
     *
     * This method takes an unformatted string and formats it into a PHP-friendly name.
     * By default, it replaces words separated by the word separator character(s) with camelCaps.
     * If `$isAction` is false, it also preserves words separated by the path separation character with an underscore, making the following word Title cased.
     * All non-alphanumeric characters are removed.
     *
     * @param string $unformatted The unformatted string to be formatted.
     * @param boolean $isAction Defaults to false. If true, the method will not preserve words separated by the path separation character with an underscore.
     * @return string The formatted string.
     */
    protected function _formatName($unformatted, $isAction = false)
    {
        return $unformatted;
    }
}
