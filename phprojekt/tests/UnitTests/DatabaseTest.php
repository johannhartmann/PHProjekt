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
 * A DBUnit test case framework.
 * Note: DBUnit is not available for PHPUnit 9+, so we extend PHPUnit\Framework\TestCase instead
 * Database-specific tests may need to be adapted or use alternative approaches
 */
abstract class DatabaseTest extends PHPUnit\Framework\TestCase {
    protected static $fixturesLoaded = false;

    public function setUp(): void {
        parent::setUp();
        Phprojekt::getInstance();

        // Load fixtures once per test run
        if (!self::$fixturesLoaded) {
            $this->loadFixtures();
            self::$fixturesLoaded = true;
        }
    }

    protected function loadFixtures() {
        $fixtureFile = __DIR__ . '/../test_fixtures.sql';
        if (file_exists($fixtureFile)) {
            $db = Phprojekt::getInstance()->getDb();

            // Execute the entire file using mysql command to handle complex statements
            $config = Phprojekt::getInstance()->getConfig();
            $dbParams = $config->database->params;

            $command = sprintf(
                'mysql -h %s -u %s -p%s %s < %s 2>&1',
                escapeshellarg($dbParams->host),
                escapeshellarg($dbParams->username),
                escapeshellarg($dbParams->password),
                escapeshellarg($dbParams->dbname),
                escapeshellarg($fixtureFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                error_log("Warning: Could not load fixtures: " . implode("\n", $output));
            }
        }
    }

    protected function getConnection() {
        /* @todo read from settings later */
        // Note: This method is kept for backwards compatibility but may not work
        // as expected without DbUnit extension
        return Phprojekt::getInstance()->getDb()->getDriver()->getConnection();
    }

    protected function getDataSet() {
        // Stub method for backwards compatibility
        return null;
    }
}

