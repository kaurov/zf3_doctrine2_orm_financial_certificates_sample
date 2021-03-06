<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\CertificateManager;
use Application\Controller\CertificateController;

/**
 * This is the factory for CertificateController. Its purpose is to instantiate the
 * controller.
 */
class CertificateControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $certificateManager = $container->get(CertificateManager::class);
        
        // Instantiate the controller and inject dependencies
        return new CertificateController($entityManager, $certificateManager);
    }
}


