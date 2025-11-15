<?php
/**
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * @copyright  Copyright (c) 2011 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 * @version    Release: 6.1.0
 */


/**
 * Tests for Index Controller
 *
 * @version    Release: 6.1.0
 * @group      calendar2
 * @group      calendar
 * @group      controller
 * @group      calendar2-controller
 * @group      calendar-controller
 */
class Calendar2_IndexController_Test extends FrontInit
{
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../../common.xml');
    }

    /**
     * Test creation and subsequent deletion of the same event.
     */
    public function testCreateAndDelete()
    {
        $this->_setTimezone(1);

        $this->setRequestUrl('Calendar2/index/jsonSave/nodeId/1/id/0');
        $this->request->getPost()->set('comments', '');
        $this->request->getPost()->set('confirmationStatus', '2');
        $this->request->getPost()->set('description', '');
        $this->request->getPost()->set('end', '2011-12-16 09:00');
        $this->request->getPost()->set('location', '');
        $this->request->getPost()->set('ownerId', '3');
        $this->request->getPost()->set('participants', '3');
        $this->request->getPost()->set('sendNotification', '0');
        $this->request->getPost()->set('start', '2011-12-16 08:00');
        $this->request->getPost()->set('summary', 'asd');
        $this->request->getPost()->set('visibility', '1');
        $response = $this->getResponse();
        $this->assertContains(IndexController::ADD_TRUE_TEXT, $response);

        $response = json_decode($response, 5, -1);
        $this->assertArrayHasKey('id', $response);
        $id = $response['id'];

        $this->_reset();
        $this->setRequestUrl("Calendar2/index/jsonDelete/id/{$id}/occurrence/2011-12-16%2007:00:00");
        $response = $this->getResponse();
        $this->assertContains(IndexController::DELETE_TRUE_TEXT, $response);

    }

    private function _setTimezone($offset)
    {
        $this->_reset();
        $this->setRequestUrl('Core/setting/jsonSave/nodeId/1/moduleName/User');
        $this->request->getPost()->set('confirmValue', '');
        $this->request->getPost()->set('email', '');
        $this->request->getPost()->set('language', 'en');
        $this->request->getPost()->set('oldValue', '');
        $this->request->getPost()->set('password', '');
        $this->request->getPost()->set('proxies[]', '');
        $this->request->getPost()->set('timeZone', "{$offset}");
        $response = $this->getResponse();
        $this->assertContains(IndexController::EDIT_TRUE_TEXT, $response);
        $this->_reset();
    }
}
