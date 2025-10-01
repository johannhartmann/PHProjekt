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
 * Timecard Extension
 *
 * This makes Timecard_Migration available to the Phprojekt library through the
 * extension api.
 */

class Timecard_Extension extends Phprojekt_Extension_Abstract
{
    /**
     * Returns the version number of the Timecard extension..
     *
     * This method provides the current version identifier for the Timecard extension module.
     * It returns a hardcoded semantic version string following the major.minor.patch versioning scheme.
     * This version information is typically used for compatibility checks, upgrade processes, or display purposes within the PHProjekt application framework.
     * @return string The version number in semantic versioning format (currently '6.3.0')
     */
    public function getVersion()
    {
        return '6.3.0';
    }

    /**
     * Creates and returns a new Timecard_Migration instance.
     *
     * This factory method instantiates and returns a new Timecard_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Timecard module.
     * The method takes no parameters and always returns a fresh instance of the migration class.
     * @return Timecard_Migration A new instance of the Timecard_Migration class
     * @see Timecard_Migration
     */
    /**
     * Creates and returns a new Timecard_Migration instance.
     *
     * This factory method instantiates and returns a new Timecard_Migration object.
     * It serves as a simple factory pattern implementation, providing a standardized way to obtain migration instances for the Timecard module.
     * The method takes no parameters and always returns a fresh instance of the migration class, which is typically used for database schema migrations or data transformations related to the Timecard extension.
     * @return Timecard_Migration A new instance of the Timecard_Migration class
     * @see Timecard_Migration
     */
    /**
     * Creates and returns a new Timecard_Migration instance.
     *
     * Factory method that instantiates and returns a new Timecard_Migration object.
     * This method implements a simple factory pattern, providing a standardized way to obtain migration instances for the Timecard module.
     * The migration object is typically used for database schema migrations or data transformations related to the Timecard extension.
     * Each call creates a fresh instance with no shared state.
     * @return Timecard_Migration A new instance of the Timecard_Migration class
     * @see Timecard_Migration
     */
    public function getMigration()
    {
        return new Timecard_Migration();
    }
}
