<?php

use DV\Doctrine\InteropEntityManagerService ;

if(! defined('DOCTRINE_ORM_WRITE'))    {
    define('DOCTRINE_ORM_WRITE' , 'orm_write');
}

if(! defined('DOCTRINE_ORM_READ'))    {
    define('DOCTRINE_ORM_READ' , 'orm_read');
}

$config = [
    'dependencies' => [
        /**
         * I might still consider moving this service configuration into the parent module (DV\Doctrine)
         */
        'aliases' => [
            #'doctrine.entitymanager.orm_default' => sprintf('doctrine.entitymanager.%s' , DOCTRINE_ORM_READ)  ,
            \DV\Doctrine\Service::class => \DV\Doctrine\Service::SERVICE_NAME,
            \Doctrine\ORM\EntityManager::class => sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ)  ,
            'doctrine.connection.orm_default' => sprintf('doctrine.connection.%s' , DOCTRINE_ORM_READ) ,
            'doctrine.migrations_configuration.orm_default' => sprintf('doctrine.migrations_configuration.%s' , DOCTRINE_ORM_READ) ,
            /**
             * default ORM will not have any special plugin like JSON or GEOTOOL plugin attached
             */
            'doctrine.entity_manager.orm_default' => sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) ,
        ],

        'factories' => [
            \DV\Authentication\DoctrineAuthenticationCredential::class => \DV\Authentication\DoctrineAuthenticationCredential::class ,
            \DV\Doctrine\Service::SERVICE_NAME => \DV\Doctrine\Service::class ,

            ##
            sprintf('doctrine.connection.%s' , DOCTRINE_ORM_READ) => [\ContainerInteropDoctrine\ConnectionFactory::class, DOCTRINE_ORM_READ],

            sprintf('doctrine.migrations_configuration.%s' , DOCTRINE_ORM_READ) => [\DV\Doctrine\Migration\ConfigurationFactory::class,DOCTRINE_ORM_READ],

            sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_READ) => [InteropEntityManagerService::class, DOCTRINE_ORM_READ],
            sprintf('doctrine.entity_manager.%s' , DOCTRINE_ORM_WRITE) => [InteropEntityManagerService::class , DOCTRINE_ORM_WRITE],

            #'doctrine.entity_manager.orm_default' => [InteropEntityManagerService::class, ['configKey' => DOCTRINE_ORM_READ]],
            ## Authentication Adapter using Object Repo
            \DV\Authentication\Adapter\ObjectRepository::class => \DV\Authentication\Adapter\ObjectRepository::class ,
            \DV\Authentication\Options\Authentication::class => \DV\Authentication\Options\Authentication::class
        ],
    ]
] ;

##
return $config ;