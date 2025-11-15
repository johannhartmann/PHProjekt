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

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\Sendmail;

/**
 * Mail class.
 */
class Phprojekt_Mail extends Message
{
    /**
     * External use (configuration.php):
     */
    const LINEEND_RN         = 0;
    const LINEEND_N          = 1;
    const TRANSPORT_SMTP     = 0;
    const TRANSPORT_SENDMAIL = 1;

    /**
     * Sets the SMTP server.
     *
     * The data is obtained from the configuration.php file.
     *
     * @return Smtp|Sendmail Object
     */
    public function setTransport()
    {
        switch (Phprojekt::getInstance()->getConfig()->mailTransport) {
            case self::TRANSPORT_SMTP:
            default:
                $smtpServer   = Phprojekt::getInstance()->getConfig()->smtpServer;
                $smtpAuth     = Phprojekt::getInstance()->getConfig()->smtpAuth;
                $smtpUser     = Phprojekt::getInstance()->getConfig()->smtpUser;
                $smtpPassword = Phprojekt::getInstance()->getConfig()->smtpPassword;
                $smtpSsl      = Phprojekt::getInstance()->getConfig()->smtpSsl;
                $smtpPort     = Phprojekt::getInstance()->getConfig()->smtpPort;

                if (empty($smtpServer)) {
                    $smtpServer = 'localhost';
                }

                $parameters = array();
                if (!empty($smtpAuth)) {
                    $parameters['auth'] = $smtpAuth;
                }
                if (!empty($smtpUser)) {
                    $parameters['username'] = $smtpUser;
                }
                if (!empty($smtpPassword)) {
                    $parameters['password'] = $smtpPassword;
                }
                if (!empty($smtpSsl)) {
                    $parameters['ssl'] = $smtpSsl;
                }
                if (!empty($smtpPort)) {
                    $parameters['port'] = $smtpPort;
                }

                if (empty($parameters)) {
                    $smtpTransport = new Smtp($smtpServer);
                } else {
                    $smtpTransport = new Smtp($smtpServer, $parameters);
                }

                break;

            case self::TRANSPORT_SENDMAIL:
                $smtpTransport = new Sendmail();
                break;
        }

        return $smtpTransport;
    }

    /**
     * Returns the string used for end of line in text mode emails, according to config file setting.
     *
     * @return string End of line characters.
     */
    /**
     * Retrieves the end-of-line character sequence based on the configured mail end-of-line setting..
     *
     * This protected method is responsible for determining the appropriate end-of-line character sequence to use for text-mode emails, based on the configuration setting `mailEndOfLine`.
     * It returns either a newline character (`
    `) or a carriage return and newline sequence (`
    `) depending on the configuration.
     * @return string The end-of-line character sequence to use for text-mode emails.
     */
    /**
     * Retrieves the appropriate end-of-line character sequence for text-mode emails based on the configured mail end-of-line setting..
     *
     * This protected method is responsible for determining the end-of-line character sequence to use for text-mode emails, based on the configuration setting `mailEndOfLine`.
     * It returns either a newline character (`
    `) or a carriage return and newline sequence (`
    `) depending on the configuration.
     * @return string The end-of-line character sequence to use for text-mode emails.
     * @note This method modifies global state.
     */
    protected function getEndOfLine()
    {
        switch (Phprojekt::getInstance()->getConfig()->mailEndOfLine) {
            case self::LINEEND_N:
                $endOfLine = "\n";
                break;
            case self::LINEEND_RN:
            default:
                $endOfLine = "\r\n";
                break;
        }

        return $endOfLine;
    }
}
