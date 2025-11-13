<?php
/**
 * Factory for Phprojekt service
 */
namespace Phprojekt\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class PhprojektFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('config');
        $dbAdapter = $container->get(\Laminas\Db\Adapter\AdapterInterface::class);

        // Get the legacy Phprojekt instance
        $phprojekt = \Phprojekt::getInstance();

        // Inject Laminas services into it
        if (method_exists($phprojekt, 'setServiceManager')) {
            $phprojekt->setServiceManager($container);
        }

        return $phprojekt;
    }
}
