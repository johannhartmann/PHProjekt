<?php
/**
 * User Controller - Laminas MVC
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
 * User Module Controller
 */
class UserController extends IndexController
{
    /**
     * Returns a list of all active users
     *
     * Returns a list of all users with:
     * - id      => id of user
     * - display => Display for the user
     *
     * @return JsonModel
     */
    public function jsonGetUsersAction()
    {
        parent::setCurrentProjectId();
        $db = \Phprojekt::getInstance()->getDb();
        $where = sprintf('status = %s', $db->quote('A'));
        $user = new \Phprojekt_User_User();
        $records = $user->fetchAll($where);

        $data = array();
        foreach ($records as $node) {
            $data['data'][] = array(
                'id' => $node->id,
                'display' => $node->displayName
            );
        }

        \Phprojekt_Converter_Json::echoConvert($data, \Phprojekt_ModelInformation_Default::ORDERING_LIST);

        return new JsonModel([]);
    }

    /**
     * Returns the detail (fields and data) of one user
     *
     * If the request parameter "id" is null or 0, the data will be all values of a "new user",
     * if the "id" is an existing user, the data will be all the values of the user.
     *
     * OPTIONAL request parameters:
     * - integer id: id of the user to consult
     *
     * @return JsonModel
     */
    public function jsonDetailAction()
    {
        $user = new \Phprojekt_User_User();
        $id = (int) $this->params()->fromQuery("id");

        $user->find($id);
        $data = array();

        $data['id'] = $user->id;
        $data['username'] = (empty($user->username)) ? "" : $user->username;
        $data['firstname'] = (empty($user->firstname)) ? "" : $user->firstname;
        $data['lastname'] = (empty($user->lastname)) ? "" : $user->lastname;
        $data['status'] = (empty($user->status)) ? "" : $user->status;
        $data['admin'] = (empty($user->admin)) ? "" : $user->admin;

        $setting = new \Phprojekt_Setting();
        $setting->setModule('User');

        $fields = $setting->getModel()->getFieldDefinition(\Phprojekt_ModelInformation_Default::ORDERING_FORM);
        $values = $setting->getList(0, $fields, $user->id);
        $values = $values[0];
        unset($values['id']);

        if (!empty($data['id'])) {
            $data = array_merge($data, $values);
        } else {
            foreach ($fields as $field) {
                if (!array_key_exists($field['key'], $values)) {
                    continue;
                }

                if (!is_null($field['default'])) {
                    $data[$field['key']] = $field['default'];
                } else {
                    $data[$field['key']] = "";
                }
            }
        }

        $records = array($data);

        $metadata = $user->getInformation(\Phprojekt_ModelInformation_Default::ORDERING_FORM);
        $metadata = $metadata->getFieldDefinition(\Phprojekt_ModelInformation_Default::ORDERING_FORM);

        $data = array(
            "metadata" => $metadata,
            "data" => $records,
            "numRows" => count($records)
        );

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }

    /**
     * Returns a list of all users the current user has proxy rights on
     *
     * Returns a list of all users with:
     * - id      => id of user
     * - display => Display for the user
     * - current => True or false if is the current user
     *
     * @return JsonModel
     */
    public function jsonGetProxyableUsersAction()
    {
        $current = \Phprojekt_Auth_Proxy::getEffectiveUserId();
        $proxyTable = new \Phprojekt_Auth_ProxyTable();
        $proxyUserIds = $proxyTable->getProxyableUsersForUserId();

        $data = array();

        foreach ($proxyUserIds as $user) {
            $data['data'][] = array(
                'id' => (int) $user->id,
                'display' => $user->displayName,
                'current' => $current == $user->id
            );
        }

        \Phprojekt_Converter_Json::echoConvert($data, \Phprojekt_ModelInformation_Default::ORDERING_LIST);

        return new JsonModel([]);
    }

    /**
     * Saves a user
     *
     * If the request parameter "id" is null or 0, the function will add a new user,
     * if the "id" is an existing user, the function will update it.
     *
     * The save action will save some values into the setting table.
     *
     * OPTIONAL request parameters:
     * - integer id: id of the user to save
     * - mixed all other user fields: All the field values to save
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));
        $this->setCurrentProjectId();

        // Settings
        $setting = new \Phprojekt_Setting();
        $setting->setModule('User');
        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $message = $setting->validateSettings($allParams);

        if (!empty($message)) {
            $type = "error";
            $id = 0;
        } else {
            if (empty($id)) {
                $model = $this->getModelObject();
                $message = \Phprojekt::getInstance()->translate(self::ADD_TRUE_TEXT);
            } else {
                $model = $this->getModelObject()->find($id);
                $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
            }

            $params = $this->setParams($allParams, $model);
            \Default_Helpers_Save::save($model, $params);

            if (empty($id)) {
                $id = $model->id;
            }

            $setting->setSettings($allParams, $id);
            $type = "success";
        }

        $return = array(
            'type' => $type,
            'message' => $message,
            'id' => $id
        );

        \Phprojekt_Converter_Json::echoConvert($return);

        return new JsonModel([]);
    }
}
