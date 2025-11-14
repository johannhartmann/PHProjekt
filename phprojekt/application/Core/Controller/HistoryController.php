<?php
/**
 * History Controller - Laminas MVC
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
 * History Module Controller
 */
class HistoryController extends IndexController
{
    /**
     * Returns the list of actions done on one item
     *
     * REQUIRES request parameters:
     * - integer moduleId: id of the module (if moduleName is sent, this is not necessary)
     * - integer itemId: id of the item
     *
     * OPTIONAL request parameters:
     * - integer userId: To filter by user id
     * - string moduleName: Name of the module (if moduleId is sent, this is not necessary)
     * - date startDate: To filter by start date
     * - date endDate: To filter by end date
     *
     * @return JsonModel
     */
    public function jsonListAction()
    {
        $moduleId = (int) $this->params()->fromQuery('moduleId', null);
        $itemId = (int) $this->params()->fromQuery('itemId', null);
        $userId = (int) $this->params()->fromQuery('userId', null);
        $moduleName = \Cleaner::sanitize('alnum', $this->params()->fromQuery('moduleName', 'Default'));
        $startDate = \Cleaner::sanitize('date', $this->params()->fromQuery('startDate', null));
        $endDate = \Cleaner::sanitize('date', $this->params()->fromQuery('endDate', null));
        $this->setCurrentProjectId();

        if (empty($moduleId)) {
            $moduleId = \Phprojekt_Module::getId($moduleName);
        }

        if (empty($itemId) || empty($moduleId)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => 'Invalid module or item']);
        }

        $history = new \Phprojekt_History();
        $data = $history->getHistoryData(null, $itemId, $moduleId, $startDate, $endDate, $userId);
        $data = array('data' => $data);

        \Phprojekt_Converter_Json::echoConvert($data);

        return new JsonModel([]);
    }
}
