<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator ;
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

    try{
        ##
        #$configurator->import(__DIR__.'/autoload/*.php');

        #$this->registerClasses($definition, 'DV\\Doctrine\\', DV_DOCTRINE_ROOT . '/src/*' , DV_DOCTRINE_ROOT.'/src/root-source/src/{Resources}');

        #if($container->has(\Doctrine\ORM\EntityManagerInterface::class))    {
        ##
        $services->alias(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) , \Doctrine\ORM\EntityManagerInterface::class)->public() ;
        #}

        #if($container->has('doctrine.orm.default_entity_manager'))    {
        ##
        $services->alias(sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) , 'doctrine.orm.default_entity_manager')->public();
        $services->alias('doctrine.entity_manager.orm_default', 'doctrine.orm.default_entity_manager')->public() ;
        # }

        #if($container->has('doctrine.connection.orm_default'))    {
        ##
        $services->alias(sprintf('doctrine.connection.%s' , DOCTRINE_ORM_READ) , 'doctrine.dbal.default_connection')->public() ;
        # }
    }
    catch (\Throwable $exception)   {
        dump($exception); exit;
    }
    ##
    return $services;
};