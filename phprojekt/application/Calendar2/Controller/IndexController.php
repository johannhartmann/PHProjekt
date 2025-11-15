<?php
/**
 * Calendar2 Index Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Calendar2\Controller;

use Application\Default\Controller\IndexController as DefaultIndexController;
use Laminas\View\Model\JsonModel;

/**
 * Calendar2 Module Controller
 */
class IndexController extends DefaultIndexController
{
    /**
     * Returns specific users by ID
     *
     * @return JsonModel
     */
    public function jsonGetSpecificUsersAction()
    {
        $ids = \Cleaner::sanitize(
            'arrayofint',
            $this->params()->fromQuery('users', $this->params()->fromPost('users', array()))
        );

        if (empty($ids)) {
            $ids[] = (int) \Phprojekt_Auth::getUserId();
        }

        $db = \Phprojekt::getInstance()->getDb();
        $where = sprintf(
            'status = %s AND id IN (%s)',
            $db->quote('A'),
            implode(", ", $ids)
        );
        $user = new \Phprojekt_User_User();
        $records = $user->fetchAll($where);

        $data = array();
        foreach ($records as $record) {
            $data['data'][] = array(
                'id' => (int) $record->id,
                'display' => $record->displayName
            );
        }

        \Phprojekt_Converter_Json::echoConvert(
            $data,
            \Phprojekt_ModelInformation_Default::ORDERING_LIST
        );

        return new JsonModel([]);
    }

    /**
     * Returns list of items with proxy user support
     *
     * @return JsonModel
     */
    public function jsonListAction()
    {
        $userId = $this->params()->fromQuery('userId', \Phprojekt_Auth_Proxy::getEffectiveUserId());

        if (!\Cleaner::validate('int', $userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => "Invalid userId '$userId'"]);
        }

        $userId = (int) $userId;

        if (!\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => "Current user has no proxy rights for this user $userId"]);
        } else {
            \Phprojekt_Auth_Proxy::switchToUserById($userId);
        }

        return parent::jsonListAction();
    }

    /**
     * Returns all events in the given period
     *
     * @return JsonModel
     */
    public function jsonPeriodListAction()
    {
        $dateStart = $this->_getDateStringParam('dateStart');
        $dateEnd = $this->_getDateStringParam('dateEnd');
        $userId = $this->params()->fromQuery('userId', (int) \Phprojekt_Auth_Proxy::getEffectiveUserId());

        if (!\Cleaner::validate('int', $userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => "Invalid userId '$userId'"]);
        }

        $userId = (int) $userId;

        if (!\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => "Current user has no proxy rights for this user $userId"]);
        } else {
            \Phprojekt_Auth_Proxy::switchToUserById($userId);
        }

        $timezone = \Phprojekt_User_User::getUserDateTimeZone();
        $start = new \Datetime($dateStart, $timezone);
        $start->setTime(0, 0, 0);
        $end = new \Datetime($dateEnd, $timezone);
        $end->setTime(23, 59, 59);

        $model = new \Calendar2_Models_Calendar2();
        $events = $model->fetchAllForPeriod($start, $end);

        \Phprojekt_Converter_Json::echoConvert(
            $events,
            \Phprojekt_ModelInformation_Default::ORDERING_FORM
        );

        return new JsonModel([]);
    }

    /**
     * Returns all events on the given day for current user
     *
     * @return JsonModel
     */
    public function jsonDayListSelfAction()
    {
        $date = $this->_getDateStringParam('date');
        $timezone = \Phprojekt_User_User::getUserDateTimeZone();
        $start = new \Datetime($date, $timezone);
        $start->setTime(0, 0, 0);
        $end = new \Datetime($date, $timezone);
        $end->setTime(23, 59, 59);

        $model = new \Calendar2_Models_Calendar2();
        $events = $model->fetchAllForPeriod($start, $end);

        \Phprojekt_Converter_Json::echoConvert(
            $events,
            \Phprojekt_ModelInformation_Default::ORDERING_FORM
        );

        return new JsonModel([]);
    }

    /**
     * Returns recurring events
     *
     * @return JsonModel
     */
    public function jsonRecurringListAction()
    {
        $userId = $this->params()->fromQuery('userId', (int) \Phprojekt_Auth_Proxy::getEffectiveUserId());

        if (!\Cleaner::validate('int', $userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => "Invalid userId '$userId'"]);
        }

        $userId = (int) $userId;

        if (!\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => "Current user has no proxy rights for user $userId"]);
        } else {
            \Phprojekt_Auth_Proxy::switchToUserById($userId);
        }

        $model = new \Calendar2_Models_Calendar2();
        $records = $model->fetchAllRecurring();

        \Phprojekt_Converter_Json::echoConvert(
            $records,
            \Phprojekt_ModelInformation_Default::ORDERING_FORM
        );

        return new JsonModel([]);
    }

    /**
     * Export calendar events to ICS format
     *
     * @return \Laminas\Http\Response
     */
    public function icsExportAction()
    {
        $userId = (int) $this->params()->fromQuery('userId', \Phprojekt_Auth_Proxy::getEffectiveUserId());

        if (!\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            $response->setContent('Forbidden');
            return $response;
        }

        \Phprojekt_Auth_Proxy::switchToUserById($userId);

        $model = new \Calendar2_Models_Calendar2();
        $events = $model->fetchAll();

        $ics = new \Calendar2_Helper_Ics();
        $content = $ics->exportEvents($events);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/calendar; charset=utf-8');
        $response->getHeaders()->addHeaderLine('Content-Disposition', 'attachment; filename="calendar.ics"');
        $response->setContent($content);

        return $response;
    }

    /**
     * Save calendar event
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));
        $userId = $this->params()->fromPost('userId', $this->params()->fromQuery('userId', null));

        if ($userId !== null && !\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => 'No proxy rights for this user']);
        }

        if ($userId !== null) {
            \Phprojekt_Auth_Proxy::switchToUserById($userId);
        }

        return parent::jsonSaveAction();
    }

    /**
     * Delete calendar event
     *
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $userId = $this->params()->fromPost('userId', $this->params()->fromQuery('userId', null));

        if ($userId !== null && !\Phprojekt_Auth_Proxy::hasProxyRightForUserById($userId)) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => 'No proxy rights for this user']);
        }

        if ($userId !== null) {
            \Phprojekt_Auth_Proxy::switchToUserById($userId);
        }

        return parent::jsonDeleteAction();
    }
}
