<?php
/**
 * Laminas Module for Default
 */
namespace Application\Default;

use Laminas\Mvc\MvcEvent;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(\Laminas\EventManager\EventInterface $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        // Add any bootstrap logic here
    }
}
