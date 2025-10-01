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
 * Project Extension
 *
 * This makes Project_Migration available to the Phprojekt library through the
 * extension api.
 */

class Project_Extension extends Phprojekt_Extension_Abstract
{
    /**
     * Returns the version number of the Project extension..
     *
     * This method provides the current version identifier for the Project extension module.
     * It returns a hardcoded semantic version string following the major.minor.patch format.
     * This version information is typically used for compatibility checks, upgrade processes, or display purposes within the PHProjekt application framework.
     * @return string The version number as a string in semantic versioning format (currently '6.1.0')
     */
    public function getVersion()
    {
        return '6.1.0';
    }

    /**
     * Creates and returns a new Project_Migration instance.
     *
     * This factory method instantiates and returns a new Project_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Project module.
     * The method takes no parameters and always returns a fresh instance of the migration class.
     * @return Project_Migration A new instance of the Project_Migration class
     * @see Project_Migration
     */
    /**
     * Creates and returns a new Project_Migration instance.
     *
     * This factory method instantiates and returns a new Project_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Project module.
     * The method takes no parameters and always returns a fresh instance of the migration class, ensuring each call produces a new object rather than reusing an existing one.
     * @return Project_Migration A new instance of the Project_Migration class
     * @see Project_Migration
     */
    /**
     * Creates and returns a new Project_Migration instance.
     *
     * This factory method instantiates and returns a new Project_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Project module.
     * The method takes no parameters and always returns a fresh instance of the migration class, ensuring each call produces a new object rather than reusing an existing one.
     * @return Project_Migration A new instance of the Project_Migration class
     * @see Project_Migration
     */
    public function getMigration()
    {
        return new Project_Migration();
    }
}
