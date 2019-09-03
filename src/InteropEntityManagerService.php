<?php
declare(strict_types=1);

namespace DV\Doctrine;

use ContainerInteropDoctrine\EntityManagerFactory ;
use DV\Service\TraitOptions;
use Psr\Container\ContainerInterface;

class InteropEntityManagerService extends EntityManagerFactory
{
    use Doctrine ;
    use TraitOptions;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @param string $configKey

    public function __construct($options=[])
    {
        if(is_string($options))    {
            $config = $options ;
            unset($options) ;
            $options['configKey'] = $config ;
        }
        ##
        elseif(! isset($options['configKey']))    {
            $options['configKey'] = 'orm_read' ;
        }

        ##
        $this->setOptions($options) ;
        ##
        $this->configKey = $options['configKey'];
    }*/

    public function __invoke(ContainerInterface $container)
    {
        $args = func_get_args() ;
        ##
        $requestName = $args[1];

        ## when the .(dot) pattern is used to call the Service name of doctrine
        if(1 <= strpos($requestName , '.'))    {
            ##
            list($doctrineKey, $operationKey , $configKey) = explode('.' , $requestName) ;
            ##
            $this->configKey = $configKey ;
        }
        ##
        $em = $this->createWithConfig($container, $this->configKey);
        ##
        $this->setDoctrineEntityManager($em) ;
        ##
        $this->setContainer($container) ;

        ##
        return $this->getDoctrineEntityManager(['jsonPlugin' => true , 'datePlugin' => true]) ;
    }
}