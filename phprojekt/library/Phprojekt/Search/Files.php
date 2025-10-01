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
 * Class for get the words from a file.
 *
 * The class provide the functions for read different type of files
 * and get all the words for save.
 */
class Phprojekt_Search_Files
{
    /**
     * Get all the words from a file.
     *
     * @param string $file     The name of the file.
     * @param string $fileType The filetype.
     *
     * @return string All the words found.
     */
    public function getWordsFromFile($file)
    {
        $fileType = $this->_getFileType($file);
        $string   = '';

        $file = PHPR_CORE_PATH . DIRECTORY_SEPARATOR
                . 'Phprojekt' . DIRECTORY_SEPARATOR
                . $file;

        if (is_readable($file)) {
            switch ($fileType) {
                default:
                    $string = implode(' ', file($file));
                    break;
            }
        }

        return $string;
    }

    /**
     * Get the FileType by its extension.
     *
     * @param string $filename The name of the file.
     *
     * @return string Extension.
     */
    /**
     * Extracts the file extension from a given filename.
     *
     * This method takes a filename as input and returns the file extension in uppercase.
     * It does this by splitting the filename on the '.' character and returning the last element of the resulting array.
     *
     * @param string $filename The name of the file to extract the extension from
     * @return string The file extension in uppercase
     * @note This method modifies filesystem.
     */
    /**
     * Extracts the file extension from a given filename.
     *
     * This private method takes a filename as input and returns the file extension in uppercase.
     * It does this by splitting the filename on the '.' character and returning the last element of the resulting array.
     *
     * @param string $filename The name of the file to extract the extension from
     * @return string The file extension in uppercase
     * @note This method modifies filesystem.
     */
    /**
     * Extracts the file extension from a given filename..
     *
     * This private method takes a filename as input and returns the file extension in uppercase.
     * It does this by splitting the filename on the '.' character and returning the last element of the resulting array.
     *
     * @param string $filename The name of the file to extract the extension from
     * @return string The file extension in uppercase
     * @note This method modifies filesystem.
     */
    private function _getFileType($filename)
    {
        $type = explode(".", $filename);

        return (strtoupper(array_pop($type)));
    }
}
