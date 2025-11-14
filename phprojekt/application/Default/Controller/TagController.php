<?php
/**
 * Tag Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

/**
 * Tag Controller
 *
 * Manages tags for items across modules
 */
class TagController extends AbstractActionController
{
    /**
     * ID required error message
     */
    const ID_REQUIRED_TEXT = "ID parameter required";

    /**
     * Get tags for a module item
     *
     * Returns metadata and data for tags:
     * - string => The tag
     * - count  => Number of occurrences
     *
     * OPTIONAL request parameters:
     * - integer id         ID of the item
     * - integer limit      Number of results
     * - string moduleName  Name of the module
     *
     * @return JsonModel
     */
    public function jsonGetTagsByModuleAction()
    {
        $tagObj = new \Phprojekt_Tags();

        // Get parameters
        $id     = (int) $this->params()->fromQuery('id', 0);
        $limit  = (int) $this->params()->fromQuery('limit', 0);
        $module = \Cleaner::sanitize('alnum', $this->params()->fromQuery('moduleName', 'Project'));
        $moduleId = (int) \Phprojekt_Module::getId($module);

        // Get tags
        if (!empty($id)) {
            $tags = $tagObj->getTagsByModule($moduleId, $id, $limit);
        } else {
            $tags = [];
        }

        $fields = $tagObj->getFieldDefinition();

        // Return both data and metadata
        return new JsonModel([
            'data' => $tags,
            'metadata' => $fields,
        ]);
    }

    /**
     * Save tags for an item
     *
     * REQUIRED request parameters:
     * - integer id  ID of the item
     *
     * OPTIONAL request parameters:
     * - string string      Tag string (words separated by spaces)
     * - string moduleName  Name of the module
     *
     * Returns:
     * - type    => 'success'
     * - message => Success message
     * - id      => 0
     *
     * @return JsonModel
     * @throws \Exception On missing ID
     */
    public function jsonSaveTagsAction()
    {
        $tagObj = new \Phprojekt_Tags();

        // Get parameters
        $id     = (int) $this->params()->fromPost('id', 0);
        $string = (string) $this->params()->fromPost('string', '');

        // Validate ID
        if (empty($id)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);

            return new JsonModel([
                'type'    => 'error',
                'message' => self::ID_REQUIRED_TEXT,
            ]);
        }

        // Get module info
        $module   = \Cleaner::sanitize('alnum', $this->params()->fromPost('moduleName', 'Project'));
        $moduleId = (int) \Phprojekt_Module::getId($module);

        // Save tags
        $tagObj->saveTags($moduleId, $id, $string);

        $message = \Phprojekt::getInstance()->translate('The Tags were added correctly');

        return new JsonModel([
            'type'    => 'success',
            'message' => $message,
            'id'      => 0,
        ]);
    }

    /**
     * Delete tags for an item
     *
     * REQUIRED request parameters:
     * - integer id  ID of the item
     *
     * OPTIONAL request parameters:
     * - string moduleName  Name of the module
     *
     * Returns:
     * - type    => 'success'
     * - message => Success message
     * - id      => 0
     *
     * @return JsonModel
     * @throws \Exception On missing ID
     */
    public function jsonDeleteTagsAction()
    {
        $tagObj = new \Phprojekt_Tags();

        // Get parameters
        $id = (int) $this->params()->fromPost('id', 0);

        // Validate ID
        if (empty($id)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);

            return new JsonModel([
                'type'    => 'error',
                'message' => self::ID_REQUIRED_TEXT,
            ]);
        }

        // Get module info
        $module   = \Cleaner::sanitize('alnum', $this->params()->fromPost('moduleName', 'Project'));
        $moduleId = (int) \Phprojekt_Module::getId($module);

        // Delete tags
        $tagObj->deleteTagsByItem($moduleId, $id);

        $message = \Phprojekt::getInstance()->translate('The Tags were deleted correctly');

        return new JsonModel([
            'type'    => 'success',
            'message' => $message,
            'id'      => 0,
        ]);
    }
}
