<?php
/**
 * Module Designer Controller - Laminas MVC
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
 * Module Designer Controller
 *
 * Handles dynamic module creation and field design
 */
class ModuleDesignerController extends IndexController
{
    /**
     * Returns the data of each field of the module
     *
     * OPTIONAL request parameters:
     * - integer id: id of the item to consult
     *
     * @return JsonModel
     */
    public function jsonDetailAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $data = array();
        $data['data'] = array();

        if (!empty($id)) {
            $module = \Phprojekt_Module::getModuleName($id);
            $model = \Phprojekt_Loader::getModel($module, $module);
            if ($model instanceof \Phprojekt_Item_Abstract) {
                $databaseManager = new \Phprojekt_DatabaseManager($model);
                $data['data']['definition'] = $databaseManager->getDataDefinition();
            } else {
                $data['data']['definition'] = 'none';
            }

            $data['data']['isUserModule'] = false;
            if (is_dir(PHPR_USER_CORE_PATH . $module)) {
                $data['data']['isUserModule'] = true;
            }
        } else {
            $data['data']['definition'] = array();
        }

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Saves the design of all fields in the module
     *
     * If the request parameter "id" is null or 0, the function will add a new module,
     * if the "id" is an existing module, the function will update it.
     *
     * The save action will try to add or update the module table itself and the database_manager.
     *
     * REQUIRES request parameters:
     * - integer id: id of the module to save
     * - string designerData: Data of the fields
     * - string name: Name of the module
     * - string label: Display of the module
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));
        $data = $this->params()->fromPost('designerData', $this->params()->fromQuery('designerData', null));
        $saveType = (int) $this->params()->fromPost('saveType', $this->params()->fromQuery('saveType', 0));
        $model = null;
        $module = \Cleaner::sanitize('alnum', $this->params()->fromPost('name', $this->params()->fromQuery('name', null)));
        $this->setCurrentProjectId();

        if (empty($module)) {
            $module = \Cleaner::sanitize('alnum', $this->params()->fromPost('label', $this->params()->fromQuery('label', null)));
        }
        $module = ucfirst(str_replace(" ", "", $module));

        if ($id > 0) {
            $model = \Phprojekt_Loader::getModel($module, $module);
        }

        $message = $this->_handleDatabaseChange($model, $module, $data, $saveType, $id);

        if (!is_null($message)) {
            \Phprojekt_Converter_Json::echoConvert($message);
            return new JsonModel([]);
        }

        $this->setCurrentProjectId();

        $message = '';

        if (empty($id)) {
            $model = new \Phprojekt_Module_Module();
            $message = \Phprojekt::getInstance()->translate('The module was added correctly');
        } else {
            $model = new \Phprojekt_Module_Module();
            $model = $model->find($id);
            $message = \Phprojekt::getInstance()->translate('The module was edited correctly');
        }

        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $allParams['name'] = $module;
        $model->saveModule($allParams);

        \Phprojekt_Module::clearCache();

        $return = array(
            'type' => 'success',
            'message' => $message,
            'id' => $model->id
        );

        \Phprojekt_Converter_Json::echoConvert($return);

        return new JsonModel([]);
    }

    /**
     * Handle database change during module save
     *
     * @param mixed $model The model instance
     * @param string $module Module name
     * @param string $data Designer data (JSON)
     * @param int $saveType Save type
     * @param int $id Module ID
     * @return array|null Error array or null on success
     */
    private function _handleDatabaseChange($model, $module, $data, $saveType, $id)
    {
        $ret = array(
            'type' => null,
            'message' => null,
            'id' => $id
        );

        if ($model instanceof \Phprojekt_Item_Abstract || $id == 0) {
            $databaseManager = new \Phprojekt_DatabaseManager($model);
            $data = json_decode($data, true);

            // Validate
            if ($databaseManager->recordValidate($data, $saveType)) {
                // Update Table Structure
                $tableData = $this->_getTableData($data);
                if (!$databaseManager->syncTable($data, $module, $tableData)) {
                    $ret['type'] = 'error';
                    $ret['message'] = \Phprojekt::getInstance()->translate('There was an error writing the table');
                } else {
                    // Remove possible id's as we are not allowed to change id's
                    foreach ($data as $key => $value) {
                        unset($data[$key]['id']);
                    }

                    // Update DatabaseManager Table
                    $databaseManager->saveData($module, $data, $tableData);

                    $ret = null;
                }
            } else {
                $error = $databaseManager->getError();
                $ret['message'] = $error['label'] . ': ' . $error['message'];
                $ret['type'] = 'error';
            }
        } else {
            $ret = null;
        }

        return $ret;
    }

    /**
     * Get the length and type from the values
     *
     * @param array $data Array of field data
     * @return array Array with data of the table
     */
    private function _getTableData($data)
    {
        $tableData = array();

        foreach ($data as $field) {
            $field['tableField'] = \Phprojekt_DatabaseManager::convertTableField($field['tableField']);

            $tableData[$field['tableField']] = array();
            $tableData[$field['tableField']]['null'] = true;
            $tableData[$field['tableField']]['default'] = null;
            foreach ($field as $key => $value) {
                $value = null;
                if ($key == 'tableType') {
                    $tableData[$field['tableField']]['type'] = $field['tableType'];
                } else if ($key == 'tableLength') {
                    $tableData[$field['tableField']]['length'] = $field['tableLength'];
                }
            }
        }

        return $tableData;
    }
}
