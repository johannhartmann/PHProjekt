<?php
/**
 * Setting Controller - Laminas MVC
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
 * Setting Module Controller
 */
class SettingController extends IndexController
{
    /**
     * Returns all modules that contain settings
     *
     * Returns a list of modules that have a Setting class, with:
     * - name  => Name of the module
     * - label => Display for the module
     *
     * @return JsonModel
     */
    public function jsonGetModulesAction()
    {
        $setting = new \Phprojekt_Setting();
        $data = $setting->getModules();

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Returns the setting fields and data for one module
     *
     * The return has:
     * - The metadata of each field
     * - The data of the setting
     * - The number of rows
     *
     * OPTIONAL request parameters:
     * - string moduleName: Name of the module
     *
     * @return JsonModel
     */
    public function jsonDetailAction()
    {
        $module = \Cleaner::sanitize('alnum', $this->params()->fromQuery('moduleName', null));
        $moduleId = (int) \Phprojekt_Module::getId($module);

        $setting = new \Phprojekt_Setting();
        $setting->setModule($module);
        $metadata = $setting->getModel()->getFieldDefinition(\Phprojekt_ModelInformation_Default::ORDERING_FORM);
        $records = $setting->getList($moduleId, $metadata);

        $data = array(
            "metadata" => $metadata,
            "data" => $records,
            "numRows" => count($records)
        );

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Saves the settings for one module
     *
     * OPTIONAL request parameters:
     * - string moduleName: Name of the module
     * - mixed all other module fields: All the field values to save
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $module = \Cleaner::sanitize('alnum', $this->params()->fromPost('moduleName', $this->params()->fromQuery('moduleName', null)));
        $this->setCurrentProjectId();

        $setting = new \Phprojekt_Setting();
        $setting->setModule($module);

        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $message = $setting->validateSettings($allParams);

        if (!empty($message)) {
            $type = "error";
        } else {
            $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
            $setting->setSettings($allParams);
            $type = "success";
        }

        $return = array(
            'type' => $type,
            'message' => $message,
            'id' => 0
        );

        \Phprojekt_Converter_Json::echoConvert($return);

        return new JsonModel([]);
    }
}
