<?php
/**
 * Administration Controller - Laminas MVC
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
 * Administration Module Controller
 *
 * Handles system configuration management
 */
class AdministrationController extends IndexController
{
    /**
     * Pre-dispatch - enforce admin-only access
     *
     * Only admin users can access these actions.
     * If the user is not an admin, returns 401 Unauthorized.
     *
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Phprojekt_Auth::isAdminUser()) {
            $response = $this->getResponse();
            $response->setStatusCode(401);
            $response->getHeaders()->addHeaderLine('WWW-Authenticate', 'AdminRequired');
            $response->sendHeaders();
            exit;
        }
    }

    /**
     * Returns all modules that contain Configuration.php file
     *
     * Returns a list of modules that have a Configuration class, with:
     * - name  => Name of the module
     * - label => Display for the module
     *
     * @return JsonModel
     */
    public function jsonGetModulesAction()
    {
        $configuration = new \Phprojekt_Configuration();
        $data = $configuration->getModules();

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Returns the configuration fields and data for one module
     *
     * The return has:
     * - The metadata of each field
     * - The data of the configuration
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

        $configuration = new \Phprojekt_Configuration();
        $configuration->setModule($module);
        $metadata = $configuration->getModel()->getFieldDefinition(\Phprojekt_ModelInformation_Default::ORDERING_FORM);
        $records = $configuration->getList($moduleId, $metadata);

        $data = array(
            "metadata" => $metadata,
            "data" => $records,
            "numRows" => count($records)
        );

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Saves the configuration for one module
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

        $configuration = new \Phprojekt_Configuration();
        $configuration->setModule($module);

        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $message = $configuration->validateConfigurations($allParams);

        if (!empty($message)) {
            $type = "error";
        } else {
            $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
            $configuration->setConfigurations($allParams);
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
