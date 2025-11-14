<?php
/**
 * Role Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Core\Controller;

use Laminas\View\Model\JsonModel;

/**
 * Role Module Controller
 */
class RoleController extends IndexController
{
    /**
     * Returns all modules and access permissions for one roleId
     *
     * Returns a list of all modules with:
     * - id       => id of the module
     * - name     => Name of the module
     * - label    => Display for the module
     * - none     => True or false for none access
     * - read     => True or false for read access
     * - write    => True or false for write access
     * - access   => True or false for access access
     * - create   => True or false for create access
     * - copy     => True or false for copy access
     * - delete   => True or false for delete access
     * - download => True or false for download access
     * - admin    => True or false for admin access
     *
     * OPTIONAL request parameters:
     * - integer id: The role id to query
     *
     * @return JsonModel
     */
    public function jsonGetModulesAccessAction()
    {
        $role = new \Phprojekt_Role_RoleModulePermissions();
        $roleId = (int) $this->params()->fromQuery('id', null);
        $modules = $role->getRoleModulePermissionsById($roleId);

        \Phprojekt_Converter_Json::echoConvert($modules);

        return new JsonModel([]);
    }
}
