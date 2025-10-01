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
 * Exception thrown when the user is not authorized to access a resource.
 *
 * This exception represents a security violation that occurs when a user attempts to access
 * a resource they are not authorized to access. It extends Phprojekt_Exception_Published and
 * should be handled appropriately by the application.
 */
class Phprojekt_Exception_NotAuthorized extends Phprojekt_Exception_Published
{
    protected $_type     = "notAuthorized";
    protected $_httpCode = 403;
}
