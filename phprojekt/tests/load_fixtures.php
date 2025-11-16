#!/usr/bin/env php
<?php
/**
 * Load test fixtures from XML files into the database
 * Replaces DBUnit functionality which is not available in PHPUnit 9+
 */

require_once __DIR__ . '/../vendor/autoload.php';

define('PHPR_ROOT_PATH', realpath(__DIR__ . '/../'));
define('PHPR_CONFIG_FILE', 'configuration.php');
define('PHPR_CONFIG_SECTION', 'testing-mysql');

require_once PHPR_ROOT_PATH . '/library/Phprojekt.php';
$phprojekt = Phprojekt::getInstance();
$db = $phprojekt->getDb();

// List of fixture files to load
$fixtureFiles = [
    __DIR__ . '/UnitTests/Phprojekt/data.xml',
    __DIR__ . '/UnitTests/Phprojekt/ActiveRecord/data.xml',
    __DIR__ . '/UnitTests/Default/data.xml',
    __DIR__ . '/UnitTests/Timecard/data.xml',
    __DIR__ . '/UnitTests/User/data.xml',
];

// Clear all tables first
echo "Clearing existing test data...\n";
$tables = [
    'tags_modules_items', 'tags', 'search_word_module', 'search_words', 'search_display',
    'history', 'frontend_message', 'item_rights', 'project_role_user_permissions',
    'project_module_permissions', 'role_module_permissions', 'role', 'module_tab_relation',
    'tab', 'module_instance', 'groups_user_relation', 'groups', 'setting', 'configuration',
    'user_contract_relation', 'contract', 'timecard', 'user_proxy', 'user', 'calendar2_user_relation',
    'calendar2_excluded_dates', 'calendar2', 'hmabtm_test_project_relation', 'hmabtm_test',
    'project', 'module', 'database_manager'
];

foreach ($tables as $table) {
    try {
        $db->query("DELETE FROM `$table`");
    } catch (Exception $e) {
        // Ignore errors for tables that might not exist
    }
}

// Function to convert XML element to SQL INSERT
function loadXmlFixture($db, $xmlFile) {
    if (!file_exists($xmlFile)) {
        echo "Skipping $xmlFile (not found)\n";
        return;
    }

    echo "Loading fixtures from " . basename(dirname($xmlFile)) . "/" . basename($xmlFile) . "...\n";

    $xml = simplexml_load_file($xmlFile);
    $recordCount = 0;

    foreach ($xml->children() as $table => $row) {
        $tableName = str_replace('_', '_', $table);
        $columns = [];
        $values = [];

        foreach ($row->attributes() as $column => $value) {
            $columns[] = str_replace('_', '_', $column);
            $values[] = $db->platform->quoteValue((string)$value);
        }

        if (empty($columns)) {
            continue;
        }

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            $tableName,
            implode(', ', $columns),
            implode(', ', $values)
        );

        try {
            $db->query($sql);
            $recordCount++;
        } catch (Exception $e) {
            echo "  ERROR inserting into $tableName:\n";
            echo "    SQL: $sql\n";
            echo "    Error: " . $e->getMessage() . "\n";
        }
    }

    echo "  Loaded $recordCount records\n";
}

// Load each fixture file
foreach ($fixtureFiles as $file) {
    loadXmlFixture($db, $file);
}

echo "\nFixtures loaded successfully!\n";
echo "\nVerifying data:\n";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM project");
$result = $stmt->execute();
echo "  Projects: " . $result->current()['cnt'] . "\n";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM user");
$result = $stmt->execute();
echo "  Users: " . $result->current()['cnt'] . "\n";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM module");
$result = $stmt->execute();
echo "  Modules: " . $result->current()['cnt'] . "\n";
$stmt = $db->query("SELECT COUNT(*) as cnt FROM role");
$result = $stmt->execute();
echo "  Roles: " . $result->current()['cnt'] . "\n";
