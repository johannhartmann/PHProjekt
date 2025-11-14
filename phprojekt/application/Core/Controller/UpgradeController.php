<?php
/**
 * Upgrade Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Core\Controller;

use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

/**
 * Upgrade Controller
 *
 * Handles PHProjekt database migration and upgrades
 */
class UpgradeController extends IndexController
{
    /**
     * Index action
     *
     * If the user is an admin and we need upgrades, print a form.
     * Else, print a message depending on the situation.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $config = \Phprojekt::getInstance()->getConfig();
        $language = \Phprojekt_Auth::getRealUser()->getSetting("language", $config->language);

        $viewData = [
            'language' => $language,
            'compressedDojo' => (bool) $config->compressedDojo,
            'frontendMsg' => (bool) $config->frontendMessages,
            'newVersion' => \Phprojekt::getVersion(),
        ];

        $extensions = new \Phprojekt_Extensions(PHPR_CORE_PATH);
        $migration = new \Phprojekt_Migration($extensions);

        if ($migration->needsUpgrade()) {
            if (!\Phprojekt_Auth::isAdminUser()) {
                $viewData['template'] = 'upgradeLocked';
            } else {
                $viewData['modules'] = $migration->getModulesNeedingUpgrade();
                $viewData['template'] = 'upgrade';
            }
        } else {
            $viewData['template'] = 'upgradeIdle';
        }

        return new ViewModel($viewData);
    }

    /**
     * Perform all upgrades
     *
     * Redirects to the index after completion.
     *
     * @return \Laminas\Http\Response
     */
    public function upgradeAction()
    {
        if (!\Phprojekt_Auth::isAdminUser()) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => 'Insufficient rights.']);
        }

        $extensions = new \Phprojekt_Extensions(PHPR_CORE_PATH);
        $migration = new \Phprojekt_Migration($extensions);

        $migration->performAllUpgrades();

        // Redirect to index
        return $this->redirect()->toUrl('index.php');
    }

    /**
     * Perform the upgrade for a single module
     *
     * The module is taken from the 'upgradeModule' parameter of the request.
     *
     * @return JsonModel
     */
    public function jsonUpgradeAction()
    {
        if (!\Phprojekt_Auth::isAdminUser()) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            return new JsonModel(['error' => 'Insufficient rights.']);
        }

        $extensions = new \Phprojekt_Extensions(PHPR_CORE_PATH);
        $migration = new \Phprojekt_Migration($extensions);

        $failed = true;
        try {
            $migration->performUpgrade(
                $this->params()->fromPost('upgradeModule', $this->params()->fromQuery('upgradeModule', null))
            );
            $failed = false;
        } catch (\Phprojekt_Migration_IKilledTheDatabaseException $e) {
            \Phprojekt::getInstance()->getLog()->debug(
                "IKilledTheDatabaseException occurred while migrating: " . $e->getFile() . ':' . $e->getLine() . "\n"
                . $e->getMessage() . "\n"
                . $e->getTraceAsString() . "\n"
            );
            \Phprojekt_Converter_Json::echoConvert(
                array(
                    'type' => 'fatalFailure',
                    'message' => 'A fatal error has occured.'
                )
            );
        } catch (\Exception $e) {
            \Phprojekt::getInstance()->getLog()->debug(
                "Exception occurred while migrating: " . $e->getFile() . ':' . $e->getLine() . "\n"
                . $e->getMessage() . "\n"
                . $e->getTraceAsString() . "\n"
            );
            \Phprojekt_Converter_Json::echoConvert(
                array(
                    'type' => 'failure',
                    'message' => 'An error has occured.'
                )
            );
        }

        if (!$failed) {
            \Phprojekt_Converter_Json::echoConvert(
                array(
                    'type' => 'success',
                    'message' => 'The module was upgraded correctly'
                )
            );
        }

        return new JsonModel([]);
    }
}
