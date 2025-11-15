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
 * @copyright  Copyright (c) 2012 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

use Laminas\Router\Http\Segment as RestRoute;

/**
 * Extend Laminas REST Route to fit PHProjekt's module system.
 */
class Phprojekt_RestRoute extends RestRoute
{
    /**
     * Check if the given controller is restful.
     *
     * This is overwritten because Laminas REST routing only allows a list of restful controllers on initialization.
     * To find out if a controller is restful, we need to check it's class. So, instead of checking all Controllers on
     * Startup, we just overwrite this function to check it on demand.
     */
    /**
     * Checks if the given controller is a RESTful controller..
     *
     * This method checks if the controller class specified by the `$moduleName` and `$controllerName` parameters is a subclass of `Phprojekt_RestController`.
     * If the class exists and is a subclass, the method returns `true`, indicating that the controller is RESTful.
     * Otherwise, it returns `false`.
     *
     * @param string $moduleName The name of the module containing the controller.
     * @param string $controllerName The name of the controller to check.
     * @return bool True if the controller is a RESTful controller, false otherwise.
     * @throws ReflectionException The controller class cannot be loaded or inspected.
     * @note This method modifies global state.
     */
    /**
     * Checks if a given controller is a RESTful controller..
     *
     * This method checks if the controller class specified by the `$moduleName` and `$controllerName` parameters is a subclass of `Phprojekt_RestController`.
     * If the class exists and is a subclass, the method returns `true`, indicating that the controller is RESTful.
     * Otherwise, it returns `false`.
     *
     * @param string $moduleName The name of the module containing the controller.
     * @param string $controllerName The name of the controller to check.
     * @return bool True if the controller is a RESTful controller, false otherwise.
     * @throws ReflectionException The controller class cannot be loaded or inspected.
     * @note This method modifies global state.
     */
    /**
     * Checks if a given controller is a RESTful controller..
     *
     * This method checks if the controller class specified by the `$moduleName` and `$controllerName` parameters is a subclass of `Phprojekt_RestController`.
     * If the class exists and is a subclass, the method returns `true`, indicating that the controller is RESTful.
     * Otherwise, it returns `false`.
     *
     * @param string $moduleName The name of the module containing the controller.
     * @param string $controllerName The name of the controller to check.
     * @return bool True if the controller is a RESTful controller, false otherwise.
     * @throws ReflectionException The controller class cannot be loaded or inspected.
     * @note This method modifies global state.
     */
    protected function _checkRestfulController($moduleName, $controllerName)
    {
        $controllerName = ucfirst($moduleName) . '_' . ucfirst($controllerName) . 'Controller';
        if (!@class_exists($controllerName)) {
            return false;
        }
        $class = new ReflectionClass($controllerName);
        if ($class->isSubclassOf('Phprojekt_RestController')) {
            return true;
        }
        return false;
    }
}
