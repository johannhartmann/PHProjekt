<?php
/**
 * Module Controller - Laminas MVC
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
 * Module Controller
 */
class ModuleController extends IndexController
{
    /**
     * String to use on error when trying to delete a system module
     */
    const CAN_NOT_DELETE_SYSTEM_MODULE = "You can not delete system modules";

    /**
     * Returns all global modules
     *
     * Returns a list of all global modules with:
     * - id     => id of the module
     * - name   => Name of the module
     * - label  => Display for the module
     *
     * Also returns in the metadata if the user is an admin or not.
     *
     * @return JsonModel
     */
    public function jsonGetGlobalModulesAction()
    {
        $modules = array();
        $model = new \Phprojekt_Module_Module();
        foreach ($model->fetchAll('active = 1 AND (save_type = 1 OR save_type = 2)', 'name ASC') as $module) {
            $modules['data'][$module->id] = array();
            $modules['data'][$module->id]['id'] = $module->id;
            $modules['data'][$module->id]['name'] = $module->name;
            $modules['data'][$module->id]['label'] = $module->label;
        }
        $modules['metadata'] = \Phprojekt_Auth::isAdminUser();

        \Phprojekt_Converter_Json::echoConvert($modules);

        return new JsonModel([]);
    }

    /**
     * Deletes a module
     *
     * Deletes the module entries, the module itself,
     * the databasemanager entry and the table itself.
     *
     * REQUIRES request parameters:
     * - integer id: id of the item to delete
     *
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));

        if (empty($id)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => self::ID_REQUIRED_TEXT]);
        }

        $model = $this->getModelObject()->find($id);

        if ($model instanceof \Phprojekt_ActiveRecord_Abstract) {
            if (is_dir(PHPR_CORE_PATH . $model->name)) {
                $response = $this->getResponse();
                $response->setStatusCode(422);
                return new JsonModel(['error' => self::CAN_NOT_DELETE_SYSTEM_MODULE]);
            }

            $databaseModel = \Phprojekt_Loader::getModel($model->name, $model->name);
            if ($databaseModel instanceof \Phprojekt_Item_Abstract) {
                $databaseManager = new \Phprojekt_DatabaseManager($databaseModel);

                if (\Default_Helpers_Delete::delete($model)) {
                    $return = $databaseManager->deleteModule();
                } else {
                    $return = false;
                }
            } else {
                $return = \Default_Helpers_Delete::delete($model);
            }

            if ($return === false) {
                $message = \Phprojekt::getInstance()->translate('The module can not be deleted');
                $type = 'error';
            } else {
                \Phprojekt::removeControllersFolders();
                $message = \Phprojekt::getInstance()->translate('The module was deleted correctly');
                $type = 'success';
            }

            $return = array(
                'type' => $type,
                'message' => $message,
                'id' => $id
            );

            \Phprojekt_Converter_Json::echoConvert($return);
            return new JsonModel([]);
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return new JsonModel(['error' => self::NOT_FOUND]);
        }
    }
}
