<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference ;
/**
 * This will also be called in the service class, Alway remember to exclude the Resources folder from the $this->>registerClasses to avoid call this file as a class
 * the problem can be very annoying
 */
if(! defined('DV_DOCTRINE_INITIALIZED'))    {
    ## load the bootstrap file
    $bootstrap = dirname(dirname(dirname(__DIR__))) . '/bootstrap.php' ;
    ##
    if(! file_exists($bootstrap))    {
        throw new \RuntimeException('bootstrap file is required to initialized the Bundle for interopability purpose') ;
    }
    ##
    require $bootstrap ;
}


return function(ContainerConfigurator $configurator) use($container)    {
    ## default configuration for services in *this* file
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
        ->public()
    ;
    ##
    $configurator->import(__DIR__.'/autoload/*.php');

    #$this->registerClasses($definition, 'DV\\Doctrine\\', DV_DOCTRINE_ROOT . '/src/*' , DV_DOCTRINE_ROOT.'/src/root-source/src/{Resources}');

    if($container->has(\Doctrine\ORM\EntityManagerInterface::class))    {
        ##
        $container->setAlias(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) , \Doctrine\ORM\EntityManagerInterface::class) ;
    }

    if($container->hasAlias('doctrine.orm.default_entity_manager'))    {
        ##
        $container->setAlias(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) , 'doctrine.orm.default_entity_manager') ;
        $container->setAlias('doctrine.entity_manager.orm_default', 'doctrine.orm.default_entity_manager') ;
    }

    if($container->has('doctrine.connection.orm_default'))    {
        ##
        $container->setAlias(sprintf('doctrine.connection.%s' , DOCTRINE_ORM_READ) , 'doctrine.connection.orm_default') ;
    }

    ##
    return $services;
};