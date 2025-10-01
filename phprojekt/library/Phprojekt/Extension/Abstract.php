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
 * Phprojekt Class for initialize the Zend Framework.
 */
abstract class PHProjekt_Extension_Abstract {
    public function init() {
    }

    /**
     * Return the extension api version to use. Current is 6.1.0.
     *
     * @return string The version.
     */
    public abstract function getVersion();

    /**
     * This function has to be implemented if the module needs to upgrade the
     * database between minor changes.
     *
     * The returned object must be a subclass of Phprojekt_Migration_Abstract or
     * null. If it is null, Phprojekt will assume that this module never needs
     * to upgrade.
     */
    /**
     * Returns the migration object for the current module, if any..
     *
     * This method is used to retrieve the migration object for the current module.
     * If the module needs to upgrade the database between minor changes, this method should return a subclass of `Phprojekt_Migration_Abstract`.
     * If the module does not require any database migrations, this method should return `null`.
     * @return Phprojekt_Migration_Abstract|null The migration object for the current module, or `null` if no migration is required.
     */
    /**
     * Returns the migration object for the current module, if any..
     *
     * This method is used to retrieve the migration object for the current module.
     * If the module needs to upgrade the database between minor changes, this method should return a subclass of `Phprojekt_Migration_Abstract`.
     * If the module does not require any database migrations, this method should return `null`.
     * @return Phprojekt_Migration_Abstract|null The migration object for the current module, or `null` if no migration is required.
     * @note This method accesses database.
     */
    /**
     * Returns the migration object for the current module, if any..
     *
     * This method is used to retrieve the migration object for the current module.
     * If the module needs to upgrade the database between minor changes, this method should return a subclass of `Phprojekt_Migration_Abstract`.
     * If the module does not require any database migrations, this method should return `null`.
     * @return Phprojekt_Migration_Abstract|null The migration object for the current module, or `null` if no migration is required.
     * @note This method accesses database.
     */
    public function getMigration()
    {
        return null;
    }
}
