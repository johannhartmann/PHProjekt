<?php
/**
 * Calendar2 REST Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Calendar2\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Calendar2 REST Controller
 *
 * RESTful controller for Calendar2 module
 */
class Calendar2Controller extends AbstractRestfulController
{
    /**
     * Return list of resources (GET request)
     *
     * @return JsonModel
     */
    public function getList()
    {
        return new JsonModel([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Return single resource (GET request with id)
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function get($id)
    {
        return new JsonModel([
            'success' => true,
            'id' => $id
        ]);
    }

    /**
     * Create a new resource (POST request)
     *
     * @param mixed $data
     * @return JsonModel
     */
    public function create($data)
    {
        return new JsonModel([
            'success' => true,
            'message' => 'Resource created'
        ]);
    }

    /**
     * Update existing resource (PUT request)
     *
     * @param mixed $id
     * @param mixed $data
     * @return JsonModel
     */
    public function update($id, $data)
    {
        return new JsonModel([
            'success' => true,
            'id' => $id,
            'message' => 'Resource updated'
        ]);
    }

    /**
     * Delete resource (DELETE request)
     *
     * @param mixed $id
     * @return JsonModel
     */
    public function delete($id)
    {
        return new JsonModel([
            'success' => true,
            'id' => $id,
            'message' => 'Resource deleted'
        ]);
    }
}
