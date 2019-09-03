<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference ;
/**
 * This will also be called in the service class, Alway remember to exclude the Resources folder from the $this->>registerClasses to avoid call this file as a class
 * the problem can be very annoying
 */
if(! defined('DV_DOCTRINE_INITIALIZED'))    {
    ## load the bootstrap file
    $bootstrap = dirname(dirname(dirname(__DIR__))) . '/boostrap.php' ;
    ##
    if(! file_exists($bootstrap))    {
        throw new \RuntimeException('bootstrap file is required to initialized the Bundle for interopability purpose') ;
    }
    ##
    require $bootstrap ;
}

$definition = new Definition();

$definition
    ->setAutowired(true)
    ->setAutoconfigured(true)
    ->setPublic(true);


## Same as before
$definition = new Definition();

$definition
    ->setAutowired(true)
    ->setAutoconfigured(true)
    ->setPublic(true);

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


/*
if($container->hasAlias(\Doctrine\ORM\EntityManager::class))    {
    ##
    $container->setAlias(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) , \Doctrine\ORM\EntityManager::class) ;
}

if($container->hasAlias('doctrine.connection.orm_default'))    {
    ##
    $container->setAlias('doctrine.connection.orm_default' , sprintf('doctrine.connection.%s' , DOCTRINE_ORM_READ) ) ;
}

## Explicitly configure the service

$container->register(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ))
                    ->setArgument(DOCTRINE_ORM_READ)
                    ->setFactory(new Reference(\DV\Doctrine\InteropEntityManagerService::class));

$container->register(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_WRITE))
                    ->setArgument(DOCTRINE_ORM_WRITE)
                    ->setFactory(new Reference(\DV\Doctrine\InteropEntityManagerService::class ));

$container->register(\DV\Authentication\Options\Authentication::class)
            ->setFactory(new Reference(\DV\Authentication\Options\Authentication::class));
##
$container->register(\DV\Authentication\Adapter\ObjectRepository::class)
            ->setFactory(new Reference());*/

#return $container ;