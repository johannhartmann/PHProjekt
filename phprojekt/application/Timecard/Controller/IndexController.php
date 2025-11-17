<?php
/**
 * Timecard Index Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Timecard\Controller;

use Application\Default\Controller\IndexController as DefaultIndexController;
use Laminas\View\Model\JsonModel;

/**
 * Timecard Module Controller
 */
class IndexController extends DefaultIndexController
{
    /**
     * Test action to verify routing - no authentication required
     *
     * @return JsonModel
     */
    public function testAction()
    {
        $response = [
            'message' => 'Timecard routing is working!',
            'controller' => get_class($this),
            'action' => 'test',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        return new JsonModel($response);
    }

    /**
     * Set current project ID - Timecard always uses INVISIBLE_ROOT
     *
     * @return void
     */
    public function setCurrentProjectId()
    {
        \Phprojekt::setCurrentProjectId(self::INVISIBLE_ROOT);
    }

    /**
     * Returns worked minutes per day
     *
     * @return JsonModel
     */
    public function workedMinutesPerDayAction()
    {
        list($start, $end) = $this->_paramToStartEndDT();
        $projects = $this->_projectsParamToArray();
        $records = \Timecard_Models_Timecard::getRecords($start, $end, $projects);

        \Phprojekt_Converter_Json::echoConvert(array('days' => $records['data']));
        return new JsonModel([]);
    }

    /**
     * Returns bookings for a specific day
     *
     * @return JsonModel
     */
    public function jsonDayListAction()
    {
        $date = \Cleaner::sanitize('date', $this->params()->fromQuery('date', date("Y-m-d")));
        $records = $this->getModelObject()->getDayRecords($date);

        \Phprojekt_Converter_Json::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_FORM);
        return new JsonModel([]);
    }

    /**
     * Returns recent projects used for bookings
     *
     * @return JsonModel
     */
    public function recentProjectsAction()
    {
        $n = (int) $this->params()->fromQuery('n', 5);
        $ownerId = \Phprojekt_Auth_Proxy::getEffectiveUserId();
        $model = $this->getModelObject();
        $records = $model->getRecentBookedProjects($ownerId, $n);

        \Phprojekt_Converter_Json::echoConvert($records);
        return new JsonModel([]);
    }

    /**
     * Returns all booked projects
     *
     * @return JsonModel
     */
    public function bookedProjectsAction()
    {
        $records = \Timecard_Models_Timecard::getBookedProjects();
        \Phprojekt_Converter_Json::echoConvert($records);
        return new JsonModel([]);
    }

    /**
     * Returns favorite projects
     *
     * @return JsonModel
     */
    public function jsonGetFavoritesProjectsAction()
    {
        $setting = new \Phprojekt_Setting();
        $setting->setModule('Timecard');

        $favorites = $setting->getSetting('favorites');
        if (!empty($favorites)) {
            $favorites = unserialize($favorites);
        } else {
            $favorites = array();
        }

        $activeRecord = new \Project_Models_Project();
        $tree = new \Phprojekt_Tree_Node_Database($activeRecord, 1);
        $tree = $tree->setup();

        $datas = array();
        if (is_array($favorites)) {
            foreach ($favorites as $projectId) {
                foreach ($tree as $node) {
                    if ($node->id == $projectId) {
                        $data = array();
                        $data['id'] = $projectId;
                        $data['display'] = $node->getDepthDisplay('title');
                        $data['name'] = $node->title;
                        $datas[] = $data;
                    }
                }
            }
        }

        \Phprojekt_Converter_Json::echoConvert($datas);
        return new JsonModel([]);
    }

    /**
     * Returns currently running booking
     *
     * @return JsonModel
     */
    public function jsonGetRunningBookingsAction()
    {
        $year = (int) $this->params()->fromQuery('year', date("Y"));
        $month = (int) $this->params()->fromQuery('month', date("m"));
        $date = (int) $this->params()->fromQuery('date', date("j"));
        $record = \Timecard_Models_Timecard::getRunningBooking($year, $month, $date);

        if ($record) {
            $data = array(
                'id' => $record['id'],
                'projectId' => $record['project_id'],
                'startTime' => substr($record['start_datetime'], 11),
                'endTime' => $record['end_time'],
                'note' => $record['notes']
            );
        } else {
            $data = null;
        }

        $return = array(
            'type' => 'success',
            'data' => $data,
            'id' => 0
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Saves a booking
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

        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $params = $this->setParams($allParams, $model);
        \Default_Helpers_Save::save($model, $params);

        $return = array(
            'type' => 'success',
            'message' => $message,
            'id' => $model->id
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Save favorites
     *
     * @return JsonModel
     */
    public function jsonFavoritesSaveAction()
    {
        $setting = new \Phprojekt_Setting();
        $setting->setModule('Timecard');

        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());
        $setting->setSettings($allParams);

        $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
        $return = array(
            'type' => 'success',
            'message' => $message,
            'id' => 0
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Export bookings to CSV
     *
     * @return \Laminas\Http\Response
     */
    public function csvListAction()
    {
        $db = \Phprojekt::getInstance()->getDb();
        $userId = \Phprojekt_Auth::getUserId();
        $year = (int) $this->params()->fromQuery('year', date("Y"));
        $month = (int) $this->params()->fromQuery('month', date("m"));
        if (strlen($month) == 1) {
            $month = '0' . $month;
        }
        $where = sprintf('(owner_id = %d AND DATE(start_datetime) LIKE %s)', (int) $userId,
            $db->quote($year . '-' . $month . '-%'));
        $this->setCurrentProjectId();
        $records = $this->getModelObject()->fetchAll($where, 'start_datetime ASC');

        \Phprojekt_Converter_Csv::echoConvert($records);

        return $this->getResponse();
    }

    /**
     * Returns booked minutes
     *
     * @return JsonModel
     */
    public function minutesBookedAction()
    {
        list($start, $end) = $this->_paramToStartEndDT();
        $projects = $this->_projectsParamToArray();
        $minutes = \Timecard_Models_Timecard::getBookedMinutes($start, $end, $projects);

        \Phprojekt_Converter_Json::echoConvert(array('minutesBooked' => $minutes));
        return new JsonModel([]);
    }

    /**
     * Set parameters for timecard booking
     *
     * @return array
     */
    public function setParams()
    {
        $args = func_get_args();
        $params = $args[0];
        $model = $args[1];

        $params['startDatetime'] = \Cleaner::sanitize('datetime', $params['startDatetime']);
        if (isset($params['endTime'])) {
            $params['endTime'] = \Cleaner::sanitize('time', $params['endTime']);
            if ($params['endTime'] == '') {
                unset($params['endTime']);
            }
        }
        $params['projectId'] = (int) $params['projectId'];
        $params['notes'] = \Cleaner::sanitize('string', $params['notes']);

        // Calculate minutes
        if (isset($params['endTime']) && isset($params['startDatetime'])) {
            $params['minutes'] = \Timecard_Models_Timecard::getDiffTime($params['endTime'],
                substr($params['startDatetime'], 11));
        } else if (!isset($params['endTime'])) {
            $params['minutes'] = 0;
        } else {
            $params['minutes'] = \Timecard_Models_Timecard::getDiffTime($params['endTime'],
                substr($model->startDatetime, 11));
        }

        return $params;
    }

    /**
     * Parse start and end date parameters
     *
     * @return array
     */
    private function _paramToStartEndDT()
    {
        $start = $this->params()->fromQuery('start', null);
        $end = $this->params()->fromQuery('end', null);

        if ($start === null || $end === null) {
            $start = new \DateTime();
            $end = new \DateTime();
            $start->setDate($start->format('Y'), $start->format('m'), 1);
            $end->setDate($end->format('Y'), $end->format('m'), 1);
            $end->add(new \DateInterval('P1M'));
        } else {
            $start = new \DateTime($start);
            $end = new \DateTime($end);
        }

        return array($start, $end);
    }

    /**
     * Parse projects parameter to array
     *
     * @return array
     */
    private function _projectsParamToArray()
    {
        $projects = $this->params()->fromQuery('projects', '');
        if ($projects === '') {
            return array();
        }
        return explode(',', $projects);
    }
}
