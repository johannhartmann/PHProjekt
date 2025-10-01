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
 * Calendar2 Extension
 *
 * This makes Calendar2_Migration available to the Phprojekt library through the
 * extension api.
 */

class Calendar2_Extension extends Phprojekt_Extension_Abstract
{
    /**
     * Returns the version number of the Calendar2 extension..
     *
     * This method provides the current version identifier for the Calendar2 extension module.
     * It returns a hardcoded semantic version string following the major.minor.patch versioning scheme.
     * This version information is typically used for compatibility checks, upgrade procedures, or display purposes within the PHProjekt application framework.
     * @return string The version number of the Calendar2 extension in semantic versioning format (currently '6.1.0')
     */
    public function getVersion()
    {
        return '6.1.0';
    }

    /**
     * Creates and returns a new Calendar2_Migration instance.
     *
     * This factory method instantiates and returns a new Calendar2_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Calendar2 module.
     * The method takes no parameters and always returns a fresh instance of the migration class.
     * @return Calendar2_Migration A new instance of the Calendar2_Migration class
     * @see Calendar2_Migration
     */
    /**
     * Factory method that creates and returns a new Calendar2_Migration instance.
     *
     * This method implements a simple factory pattern to instantiate Calendar2_Migration objects.
     * It provides a standardized way to obtain migration instances for the Calendar2 module without requiring direct instantiation by the caller.
     * The method takes no parameters and always returns a fresh instance, ensuring each call produces a new object rather than reusing existing instances.
     * @return Calendar2_Migration A new instance of the Calendar2_Migration class
     * @see Calendar2_Migration
     */
    public function getMigration()
    {
        return new Calendar2_Migration();
    }
}
