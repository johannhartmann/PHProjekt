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
    public function setUp(): void {
        parent::setUp();
        Phprojekt::getInstance();
        // Note: Metadata cache cleaning removed - using Laminas Db TableGateway instead
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

