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
 * Exception raised when the user's holiday region is not set.
 *
 * This exception is raised when attempting to retrieve the user's holiday region, but it has
 * not been set. This can occur if the necessary configuration or data is missing or incorrect.
 */
class Phprojekt_Exception_HolidayRegionNotSet extends Phprojekt_Exception_Published
{
    protected $_type     = 'holidayRegionNotSet';
    protected $_httpCode = 500;

}
