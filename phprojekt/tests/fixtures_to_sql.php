#!/usr/bin/env php
<?php
/**
 * Convert DBUnit XML fixtures to SQL INSERT statements
 */

$fixtureFiles = [
    __DIR__ . '/UnitTests/Phprojekt/data.xml',
    __DIR__ . '/UnitTests/Phprojekt/ActiveRecord/data.xml',
    __DIR__ . '/UnitTests/Default/data.xml',
    __DIR__ . '/UnitTests/Timecard/data.xml',
    __DIR__ . '/UnitTests/User/data.xml',
];

$sqlOutput = "-- Test fixture data generated from XML\n";
$sqlOutput .= "-- Disable foreign key checks for loading\n";
$sqlOutput .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Clear tables first
$sqlOutput .= "-- Clear existing data\n";
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
    $sqlOutput .= "DELETE FROM `$table`;\n";
}

$sqlOutput .= "\n-- Insert fixture data\n";

function xmlToSql($xmlFile) {
    if (!file_exists($xmlFile)) {
        return "";
    }

    $sql = "\n-- From " . basename(dirname($xmlFile)) . "/" . basename($xmlFile) . "\n";
    $xml = simplexml_load_file($xmlFile);

    foreach ($xml->children() as $table => $row) {
        $tableName = (string)$table;
        $columns = [];
        $values = [];

        foreach ($row->attributes() as $column => $value) {
            $columns[] = "`" . $column . "`";
            // Properly escape values
            $val = (string)$value;
            if ($val === '' || strtoupper($val) === 'NULL') {
                $values[] = "NULL";
            } else if (is_numeric($val) && strpos($val, '.') === false && strpos($val, 'e') === false) {
                $values[] = $val;
            } else {
                $values[] = "'" . addslashes($val) . "'";
            }
        }

        if (empty($columns)) {
            // Empty element like <configuration />
            continue;
        }

        $sql .= sprintf(
            "REPLACE INTO `%s` (%s) VALUES (%s);\n",
            $tableName,
            implode(', ', $columns),
            implode(', ', $values)
        );
    }

    return $sql;
}

foreach ($fixtureFiles as $file) {
    $sqlOutput .= xmlToSql($file);
}

$sqlOutput .= "\n-- Re-enable foreign key checks\n";
$sqlOutput .= "SET FOREIGN_KEY_CHECKS=1;\n";

// Write to file
file_put_contents(__DIR__ . '/test_fixtures.sql', $sqlOutput);
echo "Generated test_fixtures.sql\n";
