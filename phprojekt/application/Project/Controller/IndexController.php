<?php
/**
 * Project Index Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Project\Controller;

use Application\Default\Controller\IndexController as DefaultIndexController;
use Laminas\View\Model\JsonModel;

/**
 * Project Module Controller
 */
class IndexController extends DefaultIndexController
{
    /**
     * Saves the current project
     *
     * If the request parameter "id" is null or 0, the function will add a new project,
     * if the "id" is an existing project, the function will update it.
     *
     * OPTIONAL request parameters:
     * - integer id: id of the project to save
     * - mixed all other module fields: All the field values to save
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));
        $this->setCurrentProjectId();

        if (empty($id)) {
            $model = $this->getModelObject();
            $message = \Phprojekt::getInstance()->translate(self::ADD_TRUE_TEXT);
        } else {
            $model = $this->getModelObject()->find($id);
            $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
        }

        if ($model instanceof \Phprojekt_Model_Interface) {
            $node = new \Phprojekt_Tree_Node_Database($model, $id);
            $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
            $newNode = \Default_Helpers_Save::save($node, $allParams,
                (int) $this->params()->fromPost('projectId', $this->params()->fromQuery('projectId', null)));

            // Set the id since the Tree save returns different values from insert and update
            if (empty($id)) {
                $showId = $newNode->id;
            } else {
                $showId = $id;
            }

            $return = array(
                'type' => 'success',
                'message' => $message,
                'id' => $showId
            );

            \Phprojekt_Converter_Json::echoConvert($return);
            return new JsonModel([]);
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return new JsonModel(['error' => self::NOT_FOUND]);
        }
    }

    /**
     * Save some fields for many projects
     * Only edit existing projects.
     *
     * OPTIONAL request parameters:
     * - array data: Array with projectId and field as index, and the value
     *
     * @return JsonModel
     */
    public function jsonSaveMultipleAction()
    {
        $data = (array) $this->params()->fromPost('data', $this->params()->fromQuery('data', []));
        $showId = array();
        $model = $this->getModelObject();
        $success = true;
        $this->setCurrentProjectId();

        foreach ($data as $id => $fields) {
            $model->find($id);
            $node = new \Phprojekt_Tree_Node_Database($model, $id);
            try {
                $nodeId = (int) $this->params()->fromPost('nodeId', $this->params()->fromQuery('nodeId', null));
                $newNode = \Default_Helpers_Save::save($node, $fields, $nodeId);
                $showId[] = $newNode->id;
            } catch (\Exception $error) {
                $success = false;
                $showId = array($id);
                $message = sprintf("ID %d. %s", $id, $error->getMessage());
                break;
            }
        }

        if ($success) {
            $message = \Phprojekt::getInstance()->translate(self::EDIT_MULTIPLE_TRUE_TEXT);
            $resultType = 'success';
        } else {
            $resultType = 'error';
        }

        $return = array(
            'type' => $resultType,
            'message' => $message,
            'id' => implode(',', $showId)
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Returns all active modules that have the project
     *
     * Returns a list of all modules with:
     * - id        => id of the module
     * - name      => Name of the module
     * - label     => Display for the module
     * - inProject => True or false if the project has the module
     *
     * OPTIONAL request parameters:
     * - integer id: The project id to query
     * - integer nodeId: The id of the parent project
     *
     * @return JsonModel
     */
    public function jsonGetModulesProjectRelationAction()
    {
        $projectId = (int) $this->params()->fromQuery('id');
        $parentId = (int) $this->params()->fromQuery('nodeId');

        // On new entries, get the parent data
        if (empty($projectId)) {
            $projectId = $parentId;
        }

        $project = new \Project_Models_ProjectModulePermissions();
        $modules = $project->getProjectModulePermissionsById($projectId);

        \Phprojekt_Converter_Json::echoConvert($modules);
        return new JsonModel([]);
    }

    /**
     * Returns all role-user relations with the project
     *
     * Returns a list of all roles related to users under the project with:
     * - id    => id of the role
     * - name  => Name of the role
     * - users => id and display of the users with the role
     *
     * OPTIONAL request parameters:
     * - integer id: The project id to query
     * - integer nodeId: The id of the parent project
     *
     * @return JsonModel
     */
    public function jsonGetProjectRoleUserRelationAction()
    {
        $projectId = (int) $this->params()->fromQuery('id');
        $parentId = (int) $this->params()->fromQuery('nodeId');

        // On new entries, get the parent data
        if (empty($projectId)) {
            $projectId = $parentId;
        }

        $project = new \Project_Models_ProjectRoleUserPermissions();
        $roles = $project->getProjectRoleUserPermissions($projectId);

        \Phprojekt_Converter_Json::echoConvert($roles);
        return new JsonModel([]);
    }

    /**
     * Returns managed projects for current user
     *
     * @return \Laminas\Http\Response
     */
    public function managedProjectsAction()
    {
        \Phprojekt_CompressedSender::send(
            \Zend_Json_Encoder::encode(\Project_Models_Project::getProjectsManagedByUser())
        );

        return $this->getResponse();
    }
}
