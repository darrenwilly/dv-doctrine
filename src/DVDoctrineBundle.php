<?php

namespace DV\Doctrine;

use Doctrine\Common\Persistence\ConnectionRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DVDoctrineBundle extends Bundle
{

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function boot(): void
    {
        /**
         * This will also be called in the service class,
         */
        if(! defined('DV_DOCTRINE_INITIALIZED'))    {
            ## load the bootstrap file
            $bootstrap = dirname(__DIR__) . '/bootstrap.php' ;
            ##
            if(! file_exists($bootstrap))    {
                throw new \RuntimeException('bootstrap file is required to initialized the Bundle for interopability purpose') ;
            }
            ##
            require $bootstrap ;
        }

        parent::boot();

        $doctrine = $this->container->get('doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        ##
        if (!$doctrine instanceof ConnectionRegistry) {
            throw new \InvalidArgumentException('Service "doctrine" is missed in container');
        }

        /** @var \Doctrine\DBAL\Connection $connection */
        foreach ($doctrine->getConnections() as $connection) {
            /** @var \Doctrine\DBAL\Platforms\AbstractPlatform $databasePlatform */
            $databasePlatform = $connection->getDatabasePlatform();

            if (! $databasePlatform->hasDoctrineTypeMappingFor('enum') || 'string' !== $databasePlatform->getDoctrineTypeMapping('enum')) {
                $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
            }
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container) ;


    }

    /**
     * When you choose to overwrite the default convention of using DepenencyInjection folder as extension
     * @return UnconventionalExtensionClass|null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface

    public function getContainerExtension()
    {
        return new UnconventionalExtensionClass();
    }*/
}