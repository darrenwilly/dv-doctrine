<?php
declare(strict_types=1);

namespace DV\Doctrine;

use Doctrine\ORM\EntityManager;
use DV\Service\TraitOptions;
use Psr\Container\ContainerInterface;
use ContainerInteropDoctrine\EntityManagerFactory ;

class Service
{
    use Doctrine ;
    use TraitOptions ;

    const SERVICE_NAME = 'DV\Doctrine\Doctrine' ;

    public function __construct($options=[])
    {
        $this->setOptions($options)  ;
    }

    public function __invoke(ContainerInterface $container, string $requestedName) : self
    {
        ##
        $this->setContainer($container) ;
        # create the EntityManager from the config on doctrine global
        $em = self::interopEntityManager($container , $this->processServiceNameToConfigKey($requestedName)) ;
        ##
        $this->setDoctrineEntityManager($em) ;
        ##
        return $this ;
    }


    static public function interopEntityManager($container , $options=[]) : EntityManager
    {
        ##
        $options['datePlugin'] = true;
        $options['jsonPlugin'] = true ;

        ##
        if(! isset($options['configKey']))    {
            $options['configKey'] = DOCTRINE_ORM_READ ;
        }
        ##
        $em = call_user_func([EntityManagerFactory::class, $options['configKey']] , $container) ;
        ##
        $self = new self($options) ;
        $self->setDoctrineEntityManager($em);
        ##
        return $self->getDoctrineEntityManager($options) ;
    }

    protected function processServiceNameToConfigKey($name=null)
    {
        $config = [] ;

        if(false !== strpos($name , '.'))    {
            ## break the string apart and fetch the last
            $lastString = end(explode('.' , $name)) ;
            ##
            $config['configKey'] = $lastString ;
        }

        ##
        if(! isset($config['configKey']))    {
            $config ['configKey'] = DOCTRINE_ORM_READ ;
        }
        ##
        return (array) $config ;
    }
}