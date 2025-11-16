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
 * Manage tabs-module relations.
 *
 * The class return the tab on each module ID.
 */
class Phprojekt_Tabs
{
    /**
     * Saves the cache for our tab-module entries, to minimize database lookups.
     *
     * @var array
     */
    protected static $_cache = null;

    /**
     * Receives all tabs <-> moduleId combinations from the database.
     *
     * The method returns an array of the following format:
     *  array( MODULEID => array(TABID => TABLABEL),
     *         MODULEID => array(TABID => TABLABEL));
     *
     * @param integer $moduleId The Module ID.
     *
     * @return array Array with 'id' and 'label'.
     */
    protected static function _getCachedIds($moduleId)
    {
        if (isset(self::$_cache[$moduleId]) && null !== self::$_cache[$moduleId]) {
            return self::$_cache[$moduleId];
        }

        $db  = Phprojekt::getInstance()->getDb();
        $sql = new \Laminas\Db\Sql\Sql($db);
        $select = $sql->select()
                     ->from(array('t' => 'tab'))
                     ->join(array('rel' => 'module_tab_relation'),
                            't.id = rel.tab_id',
                            [])
                     ->where(['rel.module_id' => (int) $moduleId]);
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $rows = array();
        foreach ($result as $row) {
            $rows[] = $row;
        }

        // Set the index 0, is not used but is needed for create other index
        self::$_cache[0] = array();

        self::$_cache[$moduleId] = array();
        foreach ($rows as $row) {
           self::$_cache[$moduleId][] = array('id'    => $row['id'],
                                              'label' => $row['label']);
        }

        return self::$_cache[$moduleId];
    }

    /**
     * Returns the tabs for a given module.
     *
     * @param string $moduleId The Module ID.
     *
     * @return array Array with 'id' and 'label'.
     */
    public static function getTabsByModule($moduleId)
    {
        return self::_getCachedIds($moduleId);
    }

    /**
     * Returns all the tabs.
     *
     * @return array Rowset of results.
     */
    public static function getTabs()
    {
        $db  = Phprojekt::getInstance()->getDb();
        $sql = new \Laminas\Db\Sql\Sql($db);
        $select = $sql->select()->from('tab');
        $stmt = $sql->prepareStatementForSqlObject($select);
        $result = $stmt->execute();
        $rows = array();
        foreach ($result as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Save/update the tab only.
     *
     * @param string  $label The tab label.
     * @param integer $id    The tab ID if exists.
     *
     * @return integer Tab ID.
     */
    public function saveTab($label, $id = 0)
    {
        $db = Phprojekt::getInstance()->getDb();
        $sql = new \Laminas\Db\Sql\Sql($db);

        if ($id > 0) {
            $update = $sql->update('tab');
            $update->set(['label' => $label]);
            $update->where(['id' => (int) $id]);
            $statement = $sql->prepareStatementForSqlObject($update);
            $statement->execute();
            return $id;
        } else {
            $insert = $sql->insert('tab');
            $insert->values(['label' => $label]);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $result = $statement->execute();
            return $result->getGeneratedValue();
        }
    }

    /**
     * Save the tab-module relation.
     *
     * @param array   $tabIds   Wrray with tab ID.
     * @param integer $moduleId The module ID.
     *
     * @return void
     */
    /**
     * Saves the relationship between a module and one or more tabs..
     *
     * This method updates the module-tab relationship in the database.
     * It first deletes any existing relationships for the given module ID, then inserts new relationships for each of the provided tab IDs.
     *
     * @param array $tabIds An array of tab IDs to associate with the module.
     * @param integer $moduleId The ID of the module to update the tab relationships for.
     * @return void This method does not return a value.
     * @note This method accesses database.
     */
    /**
     * Saves the relationship between a module and one or more tabs..
     *
     * This method updates the module-tab relationship in the database.
     * It first deletes any existing relationships for the given module ID, then inserts new relationships for each of the provided tab IDs.
     *
     * @param array $tabIds An array of tab IDs to associate with the module.
     * @param integer $moduleId The ID of the module to update the tab relationships for.
     * @return void This method does not return a value.
     * @note This method accesses database.
     */
    /**
     * Saves the relationship between a module and one or more tabs..
     *
     * This method updates the module-tab relationship in the database.
     * It first deletes any existing relationships for the given module ID, then inserts new relationships for each of the provided tab IDs.
     *
     * @param array $tabIds An array of tab IDs to associate with the module.
     * @param integer $moduleId The ID of the module to update the tab relationships for.
     * @return void This method does not return a value.
     * @note This method accesses database.
     */
    public function saveModuleTabRelation($tabIds, $moduleId)
    {
        $db = Phprojekt::getInstance()->getDb();
        $sql = new \Laminas\Db\Sql\Sql($db);

        // Delete existing relations
        $delete = $sql->delete('module_tab_relation');
        $delete->where(['module_id' => (int) $moduleId]);
        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();

        // Insert new relations
        if (!is_array($tabIds)) {
            $tabIds = array($tabIds);
        }
        foreach ($tabIds as $tabId) {
            $insert = $sql->insert('module_tab_relation');
            $insert->values([
                'tab_id'    => (int) $tabId,
                'module_id' => (int) $moduleId
            ]);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $statement->execute();
        }
    }
}
