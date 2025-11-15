<?php
/**
 * HTTP Exception for Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Default\Exception;

/**
 * HTTP Exception
 *
 * Replacement for Zend_Controller_Action_Exception with proper HTTP status code support.
 * Compatible with Laminas MVC error handling.
 *
 * Usage:
 *   throw new HttpException('Error message', 400);
 *   throw new HttpException('Unauthorized', 401);
 *   throw new HttpException('Forbidden', 403);
 *   throw new HttpException('Not found', 404);
 *   throw new HttpException('Unprocessable entity', 422);
 */
class HttpException extends \RuntimeException
{
    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code HTTP status code (default 500)
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct($message = '', $code = 500, \Throwable $previous = null)
    {
        // Ensure code is a valid HTTP status code
        if ($code < 100 || $code > 599) {
            $code = 500;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->getCode();
    }
}
