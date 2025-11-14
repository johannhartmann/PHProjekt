<?php
/**
 * Project REST Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Project\Controller;

use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;

/**
 * Project REST Controller
 *
 * RESTful controller for Project module
 * Inherits RESTful methods from AbstractRestfulController
 */
class ProjectController extends AbstractRestfulController
{
    /**
     * Return list of resources (GET request)
     *
     * @return JsonModel
     */
    public function getList()
    {
        // Default implementation - can be overridden
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
        // Default implementation - can be overridden
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
        // Default implementation - can be overridden
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
        // Default implementation - can be overridden
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
        // Default implementation - can be overridden
        return new JsonModel([
            'success' => true,
            'id' => $id,
            'message' => 'Resource deleted'
        ]);
    }
}
