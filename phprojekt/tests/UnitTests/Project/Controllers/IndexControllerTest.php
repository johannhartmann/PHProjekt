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
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */


/**
 * Tests for Index Controller
 *
 * @group      project
 * @group      controller
 * @group      project-controller
 */
class Project_IndexController_Test extends FrontInit
{
    protected function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../../common.xml');
    }

    /**
     * Test of json save Project -in fact, default json save
     */
    public function testJsonSave()
    {
        $this->setRequestUrl('Project/index/jsonSave/');
        $this->request->getPost()->set('id', null);
        $this->request->getPost()->set('title', 'test');
        $this->request->getPost()->set('startDate', '2008-08-07');
        $this->request->getPost()->set('endDate', '2020-08-31');
        $this->request->getPost()->set('priority', 2);
        $this->request->getPost()->set('projectId', 1);
        $this->request->getPost()->set('nodeId', 1);
        $response = $this->getResponse();
        $this->assertContains(Project_IndexController::ADD_TRUE_TEXT, $response);
    }

    /**
     * Test of json save  multiple Project
     */
    public function testJsonSaveMultiple()
    {
        $this->setRequestUrl('Project/index/jsonSaveMultiple/');
        $this->request->getPost()->set('data[1][notes]', 'test');
        $this->request->getPost()->set('nodeId', 1);
        $response = $this->getResponse();
        $this->assertContains(Project_IndexController::EDIT_MULTIPLE_TRUE_TEXT, $response);
    }

    /**
     * Test the get all the modules active and their relation with the projectId
     */
    public function testJsonGetModulesProjectRelation()
    {
        $this->setRequestUrl('Project/index/jsonGetModulesProjectRelation/');
        $this->request->getPost()->set('id', 2);
        $response = $this->getResponse();
        $this->assertEquals(
            json_encode(
                array(
                    'data' => array(
                        1 => array(
                            'id' => 1,
                            'name' => 'Project',
                            'label' => 'Project',
                            'inProject' => true
                        )
                    )
                )
            ),
            $response
        );
    }

    /**
     * Test the get all the role-user relation with the projectId
     */
    public function testJsonGetProjectRoleUserRelation()
    {
        $this->setRequestUrl('Project/index/jsonGetProjectRoleUserRelation/');
        $this->request->getPost()->set('id', 1);
        $response = $this->getResponse();
        $this->assertContains('{"1":{"id":1,"name":"Admin",', $response);
    }

    /**
     * Test the multiple save with error
     */
    public function testJsonSaveMultipleError()
    {
        $this->setRequestUrl('Project/index/jsonSaveMultiple');
        $items = array(2 => array('projectId' => '2'));
        $this->request->getPost()->set('data', $items);
        $this->request->getPost()->set('nodeId', 1);
        $response = json_decode($this->getResponse());
        $expected = array(
            'type' => 'error',
            'message' => 'ID 2. Parent: The project can not be saved under itself',
            'id' => 2
        );
        $this->assertEquals($expected, $response);
    }
}
