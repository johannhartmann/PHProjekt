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
 * @copyright  Copyright (c) 2011 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

/**
 *
 */
final class Phprojekt_Right
{
    /**
     * Retrieves the access control list (ACL) for the specified items and merges it with the user's role-based permissions..
     *
     * This method retrieves the item-level access control list (ACL) for the specified items and the user's role-based permissions, and then merges them to determine the effective permissions for the user on those items.
     * The resulting permissions are returned as an associative array.
     *
     * @param int $moduleId The ID of the module to which the items belong.
     * @param int $projectId The ID of the project to which the items belong.
     * @param int $userId The ID of the user for whom the permissions are being retrieved.
     * @param array $itemIds An array of item IDs for which the permissions are being retrieved.
     * @return array An associative array of item IDs and their corresponding permissions for the specified user.
     * @throws null No exceptions are explicitly raised or documented in the code.
     * @note This method accesses database.
     * @see Phprojekt_Item_Rights::getItemRights
     * @see Phprojekt_Right::mergeWithRole
     */
    /**
     * Retrieves the effective permissions for the specified items and user..
     *
     * This method retrieves the item-level access control list (ACL) for the specified items and the user's role-based permissions, and then merges them to determine the effective permissions for the user on those items.
     * The resulting permissions are returned as an associative array.
     *
     * @param int $moduleId The ID of the module to which the items belong.
     * @param int $projectId The ID of the project to which the items belong.
     * @param int $userId The ID of the user for whom the permissions are being retrieved.
     * @param array $itemIds An array of item IDs for which the permissions are being retrieved.
     * @return array An associative array of item IDs and their corresponding permissions for the specified user.
     * @note This method accesses database.
     * @see Phprojekt_Item_Rights::getItemRights
     * @see Phprojekt_Right::mergeWithRole
     */
    public static function getRightsForItems($moduleId, $projectId, $userId, array $itemIds)
    {
        $acl = Phprojekt_Item_Rights::getItemRights($moduleId, $itemIds, $userId);
        return self::mergeWithRole($moduleId, $projectId, $userId, $acl);
    }

    /**
     * Merges the given item rights with the user's role rights for the specified module and project..
     *
     * This method takes the item rights for a set of items in a module and project, and combines them with the user's role rights for that module and project.
     * It determines the effective access level for each item based on the user's role permissions, and returns the updated item rights object.
     *
     * @param int $moduleId The ID of the module to merge the rights for.
     * @param int $projectId The ID of the project to merge the rights for.
     * @param int $userId The ID of the user to merge the rights for.
     * @param array $itemRights An associative array of item IDs and their corresponding access masks.
     * @return array The updated item rights array with the merged access levels.
     * @note This method accesses database.
     * @see Phprojekt_RoleRights
     * @see Phprojekt_Acl
     */
    public static function mergeWithRole($moduleId, $projectId, $userId, $itemRights)
    {
        /* there is currently only an implementation for standard modules with
         * save type NORMAL */
        if (Phprojekt_Module::getSaveType($moduleId) == Phprojekt_Module::TYPE_NORMAL) {
            $roleRights      = new Phprojekt_RoleRights($projectId, $moduleId, 0, $userId);
            $roleRightRead   = $roleRights->hasRight('read');
            $roleRightWrite  = $roleRights->hasRight('write');
            $roleRightCreate = $roleRights->hasRight('create');
            $roleRightAdmin  = $roleRights->hasRight('admin');

            // Map roles with item rights and make one array
            foreach ($itemRights as $itemId => $accessMask) {
                $access = Phprojekt_Acl::NONE;

                if ($roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::ADMIN;
                }

                if ($roleRightRead || $roleRightWrite || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::DOWNLOAD;
                }

                if ($roleRightWrite || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::DELETE;
                }

                if ($roleRightWrite || $roleRightCreate || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::COPY;
                }

                if ($roleRightWrite || $roleRightCreate || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::CREATE;
                }

                if ($roleRightRead || $roleRightWrite || $roleRightCreate || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::ACCESS;
                }

                if ($roleRightWrite || $roleRightCreate || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::WRITE;
                }

                if ($roleRightRead || $roleRightWrite || $roleRightAdmin) {
                    $access |= $accessMask & Phprojekt_Acl::READ;
                }

                $itemRights[$itemId] = $access;
            }
        }

        return $itemRights;
    }
}
