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
 * Timecard Migration
 *
 * Migration routines for the timecard module.
 */
class Timecard_Migration extends Phprojekt_Migration_Abstract
{
    /**
     * The database on which to migrate
     *
     * @var \Laminas\Db\Adapter\Adapter_Adapter_Abstract
     */
    protected $_db;

    /**
     * Return the current module version.
     *
     * Implements Phprojekt_Migration_Abstract->getCurrentModuleVersion
     *
     * @return String Version
     */
    public function getCurrentModuleVersion()
    {
        return '6.3.0';
    }

    /**
     * Upgrade to the latest version.
     *
     * @param String $currentVersion Phprojekt version string indicating our
     *                               current version
     * @param \Laminas\Db\Adapter\Adapter $db The database to use
     *
     * @return void
     * @throws Exception On Errors
     */
    /**
     * Performs database schema and data migrations for the Timecard module based on the current version.
     *
     * This method handles incremental database upgrades for the Timecard module by comparing the current version against specific version thresholds and executing appropriate migration queries.
     * For versions prior to 6.1.4, it generates unique URIs and UIDs for timecard records using the HTTP host.
     * For versions prior to 6.3.0, it performs cleanup operations by removing item rights, role module permissions, and module entries that are not in the whitelist (Timecard, Project, Calendar2), and removes a specific database_manager entry for the Project contact_id field.
     * The method sets the timezone to UTC before performing any operations.
     *
     * @param string $currentVersion The current version string of Phprojekt being upgraded from, used to determine which migration steps to execute
     * @param \Laminas\Db\Adapter\Adapter $db The database adapter instance used for executing migration queries
     * @return void This method does not return a value
     * @throws Exception Database query execution fails or other errors occur during the migration process
     * @note This method accesses database, modifies global state, depends on current time, and makes network calls.
     * @see Phprojekt::compareVersion
     * @see Timecard.Migration.parseDbFile
     * @see \Laminas\Http\PhpEnvironment\Request::getHttpHost
     */
    /**
     * Performs incremental database schema and data migrations for the Timecard module based on version comparisons.
     *
     * This method executes version-specific database migrations for the Timecard module by comparing the current version against known upgrade thresholds.
     * For versions prior to 6.1.4, it generates unique URIs and UIDs for timecard records using UUID and the HTTP host suffix, then adds a unique constraint on the uri column.
     * For versions prior to 6.3.0, it performs cleanup operations by removing item rights, role module permissions, and module entries that are not in a whitelist (Timecard, Project, Calendar2), and deletes a specific database_manager entry for the Project contact_id field.
     * The method sets the default timezone to UTC before performing any operations and parses the Timecard database file.
     *
     * @param string $currentVersion The current version string of PHProjekt being upgraded from, used to determine which migration steps to execute through version comparison
     * @param \Laminas\Db\Adapter\Adapter $db The database adapter instance used for executing migration queries and stored in the instance variable $_db
     * @return void This method does not return a value
     * @throws \Laminas\Db\Exception\ExceptionInterface Database query execution fails during migration operations
     * @throws Exception HTTP request initialization fails or other runtime errors occur during migration
     * @note This method accesses database, modifies global state, depends on current time, and makes network calls.
     * @see Phprojekt::compareVersion
     * @see Timecard_Migration::parseDbFile
     * @see \Laminas\Http\PhpEnvironment\Request::getHttpHost
     * @see Phprojekt::getInstance
     */
    public function upgrade($currentVersion, $db)
    {
        date_default_timezone_set('UTC');
        $this->_db = $db;
        $this->parseDbFile('Timecard');

        if (Phprojekt::compareVersion($currentVersion, '6.1.4') < 0) {
            // Use native PHP to get HTTP host (replaces ZF1 HTTP request)
            $httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $uidSuffix = "@phprojekt6-" . $httpHost;
            Phprojekt::getInstance()->getDB()->query(
                "UPDATE timecard SET uri = id, uid = CONCAT(UUID(), \"{$uidSuffix}\");"
            );
            // This is mysql-only. Not sure if this is the ultimate way to go here.
            Phprojekt::getInstance()->getDB()->query('ALTER TABLE timecard ADD UNIQUE (uri)');
        }

        if (Phprojekt::compareVersion($currentVersion, '6.3.0') < 0) {
            Phprojekt::getInstance()->getDB()->query(
                "DELETE ir
                   FROM item_rights ir, module m
                  WHERE ir.module_id = m.id
                    AND m.name NOT IN ('Timecard', 'Project', 'Calendar2')");
            Phprojekt::getInstance()->getDB()->query(
                "DELETE rmp
                   FROM role_module_permissions rmp, module m
                  WHERE rmp.module_id = m.id
                    AND m.name NOT IN ('Timecard', 'Project', 'Calendar2')");
            Phprojekt::getInstance()->getDB()->query(
                "DELETE FROM module
                  WHERE name NOT IN ('Timecard', 'Project', 'Calendar2')");
            Phprojekt::getInstance()->getDB()->query(
                "DELETE FROM database_manager
                  WHERE table_name = 'Project'
                    AND table_field = 'contact_id'");
        }
    }

}
