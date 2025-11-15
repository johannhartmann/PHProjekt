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
 * Tests Calendar2 Model
 *
 * @version    Release: 6.1.0
 * @group      calendar2
 * @group      calendar
 * @group      caldav
 */
class Calendar2_Caldav_CalendarBackend_Test extends FrontInit
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../../common.xml');
    }

    /**
     * Test whether splitting an recurring event saves the new event under a new UID.
     * See http://jira.opensource.mayflower.de/jira/browse/PHPROJEKT-298 for the ratio behind this.
     */
    public function testSplittingRecurrenceGivesNewUidAndUri()
    {
        $this->setRequestUrl('Calendar2/index/jsonSave/nodeId/1/id/0');
        $this->request->getPost()->set('comments', '');
        $this->request->getPost()->set('confirmationStatus', '2');
        $this->request->getPost()->set('description', '');
        $this->request->getPost()->set('end', '2011-12-01 09:00');
        $this->request->getPost()->set('location', '');
        $this->request->getPost()->set('ownerId', '2');
        $this->request->getPost()->set('participants', '2');
        $this->request->getPost()->set('rrule', 'FREQ=DAILY;INTERVAL=1;BYDAY=');
        $this->request->getPost()->set('sendNotification', '0');
        $this->request->getPost()->set('start', '2011-12-01 08:00');
        $this->request->getPost()->set('summary', 'test');
        $this->request->getPost()->set('visibility', '1');
        $response = $this->getResponse();
        $this->assertContains(IndexController::ADD_TRUE_TEXT, $response);

        $response = json_decode($response);
        $this->assertArrayHasKey('id', $response);
        $firstId = $response['id'];

        $this->_reset();

        $tzOffset = (int) Phprojekt_Auth_Proxy::getEffectiveUser()->getSetting('timeZone', '0');
        $hour = 8 - $tzOffset;
        $hour = sprintf('%02d', $hour);
        $this->setRequestUrl("Calendar2/index/jsonSave/nodeId/1/id/{$firstId}/occurrence/2011-12-03%20{$hour}:00:00");
        $this->request->getPost()->set('comments', '');
        $this->request->getPost()->set('confirmationStatus', '2');
        $this->request->getPost()->set('description', '');
        $this->request->getPost()->set('end', '2011-12-03 09:00:00');
        $this->request->getPost()->set('location', '');
        $this->request->getPost()->set('multipleEvents', 'true');
        $this->request->getPost()->set('occurrence', '2011-12-03 08:00:00');
        $this->request->getPost()->set('ownerId', '2');
        $this->request->getPost()->set('participants', '2');
        $this->request->getPost()->set('rrule', 'FREQ=DAILY;INTERVAL=1;BYDAY=');
        $this->request->getPost()->set('sendNotification', '0');
        $this->request->getPost()->set('start', '2011-12-03 08:00:00');
        $this->request->getPost()->set('summary', 'something else');
        $this->request->getPost()->set('visibility', '1');
        $response = $this->getResponse();
        $this->assertContains(IndexController::EDIT_TRUE_TEXT, $response);

        $response = json_decode($response);
        $this->assertArrayHasKey('id', $response);
        $secondId = $response['id'];

        $first = new Calendar2_Models_Calendar2();
        $first->find($firstId);

        $second = new Calendar2_Models_Calendar2();
        $second->find($secondId);
        $this->assertNotEquals($first->uid, $second->uid);
        $this->assertNotEquals($first->uri, $second->uri);
    }
}
