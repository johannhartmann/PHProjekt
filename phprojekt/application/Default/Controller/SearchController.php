<?php
/**
 * Search Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

/**
 * Search Controller
 *
 * Handles search functionality across modules and tags
 */
class SearchController extends AbstractActionController
{
    /**
     * Search for words across modules and tags
     *
     * Returns a list of items that match the search words, sorted by module.
     *
     * Result structure:
     * - search => array of search results
     *   - id            => ID of the item found
     *   - moduleId      => ID of the module
     *   - moduleName    => Name of the module
     *   - moduleLabel   => Display label for the module
     *   - firstDisplay  => First display field (e.g., title)
     *   - secondDisplay => Second display field (e.g., notes)
     *   - projectId     => Parent project ID of the item
     * - tags => array of tag results
     *
     * REQUIRED request parameters:
     * - string words  Search string (words separated by spaces)
     *
     * OPTIONAL request parameters:
     * - integer count  Number of results to return
     * - integer start  Offset for pagination
     *
     * @return JsonModel
     */
    public function jsonSearchAction()
    {
        // Get search parameters
        $words  = (string) $this->params()->fromQuery('words', '');
        $count  = (int) $this->params()->fromQuery('count', null);
        $offset = (int) $this->params()->fromQuery('start', null);

        // Perform search
        $search = new \Phprojekt_Search();
        $tags   = new \Phprojekt_Tags();

        $searchResults = $search->search($words, $count);
        $tagResults    = $tags->search($words, $count);

        // Return combined results
        return new JsonModel([
            'search' => $searchResults,
            'tags'   => $tagResults,
        ]);
    }
}
