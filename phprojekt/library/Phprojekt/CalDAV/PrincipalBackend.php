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

/**
 * Calendar2 Caldav Principal Backend
 *
 * This class implements a principal backend for sabredav
 */
class Phprojekt_CalDAV_PrincipalBackend implements Sabre_DAVACL_IPrincipalBackend
{
    public function getPrincipalsByPrefix($prefixPath)
    {
        // TODO: Implement me
        throw new Exception('not implemented. $prefixPath = ' . $prefixPath);
        return array();
    }

    public function getPrincipalByPath($path)
    {
        $user = new Phprojekt_User_User();
        $user = $user->findByUsername(preg_filter('|.*principals/([^/]+)$|', '$1', $path));
        if (is_null($user)) {
            throw new Exception("Principal not found for path $path");
        }

        return array(
            'id'                => $user->id,
            'uri'               => "principals/{$user->username}",
            '{DAV:}displayname' => $user->username,
            '{http://sabredav.org/ns}email-address' => $user->getSetting('email')
        );
    }

    public function getGroupMemberSet($principal)
    {
        throw new Exception('not implemented. $principal = ' . $principal);
    }

    public function getGroupMembership($principal)
    {
        return array();
    }

    public function setGroupMemberSet($principal, array $members)
    {
        throw new Exception('not implemented. $principal = ' . $principal);
    }

    /**
     * Updates a CalDAV principal.
     *
     * This method is a placeholder implementation that always returns false.
     * It is intended to be overridden by a concrete implementation that updates the properties of a CalDAV principal based on the provided mutations.
     *
     * @param string $path The path of the principal to be updated
     * @param array $mutations An array of property mutations to apply to the principal
     * @return bool Always returns false in this implementation
     */
    public function updatePrincipal($path, $mutations)
    {
        return false;
    }

    /**
     * Searches for principals (users or groups) matching the given search properties..
     *
     * This method searches for principals (users or groups) in the CalDAV backend that match the provided search properties.
     * The search is performed on the prefix path specified, and the results are returned as an array.
     *
     * @param string $prefixPath The prefix path to search within
     * @param array $searchProperties An array of search properties to match against
     * @return array An array of principals (users or groups) matching the search criteria
     */
    /**
     * Searches for principals (users or groups) matching the given search properties.
     *
     * This method searches for principals (users or groups) in the CalDAV backend that match the provided search properties.
     * The search is performed on the prefix path specified, and the results are returned as an array.
     *
     * @param string $prefixPath The prefix path to search within
     * @param array $searchProperties An array of search properties to match against
     * @return array An array of principals (users or groups) matching the search criteria
     * @note This method accesses database.
     */
    public function searchPrincipals($prefixPath, array $searchProperties)
    {
        return array();
    }
}
