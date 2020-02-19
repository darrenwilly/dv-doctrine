<?php

declare(strict_types=1);

namespace DV\Doctrine ;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use DV\Doctrine\DBAL\Type\DateTimeToTimestampOutputType;
use DV\Doctrine\DBAL\Type\JsonType;
use DV\MicroService\TraitContainer;
use ReflectionObject ;
use ReflectionProperty ;
use Laminas\Stdlib\Parameters;
use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql as DqlFunctions;

trait Doctrine
{
    use TraitContainer ;

	public static $default_entity_alias = 'a';
	
	public static $foreign_entity_alias = 'b';
	
	public static $intersecting_entity_alias = 'c';
	
	public static $intersecting_entity_alias2 = 'd' ;
	
	public static $intersecting_entity_alias3 = 'e' ;
	
	public static $intersecting_entity_alias4 = 'f' ;
	
	public static $intersecting_entity_alias5 = 'g' ;
	public static $intersecting_entity_alias6 = 'h' ;
	public static $intersecting_entity_alias7 = 'i' ;
	public static $intersecting_entity_alias8 = 'j' ;
	public static $intersecting_entity_alias9 = 'k' ;
	public static $intersecting_entity_alias10 = 'l' ;
	public static $intersecting_entity_alias11 = 'm' ;
	public static $intersecting_entity_alias12 = 'n' ;
	public static $intersecting_entity_alias13 = 'o' ;
	public static $intersecting_entity_alias14 = 'p' ;
	public static $intersecting_entity_alias15 = 'q' ;

	protected $em ;

    protected $_repository_name ;

    protected $_alias ;


    /**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEntityManager($options=[]) : EntityManager
	{
		if(null == $this->em)	{
            ##
            if(! isset($options['entityIdentifier']))    {
                ##
                $options['entityIdentifier'] = 'doctrine.entity_manager.orm_read' ;
            }
            ##
            $entityIdentifier = $options['entityIdentifier'] ;

            ##
            $em = $this->getLocator($entityIdentifier) ;
            ##
            if(! $em instanceof EntityManager)    {
                throw new RuntimeException( 'Invalid Doctrine Entity Manager created', 500) ;
            }
            ##
			$this->setDoctrineEntityManager($em) ;
		}

        ## register doctrine entity Type Mapping
        $this->registerDoctrineDatatypeExtras($this->em) ;

        if(isset($options['datePlugin'])) {
            $config = $this->em->getConnection()->getConfiguration();
            $config->addCustomDatetimeFunction('DATEFORMAT', \DV\Doctrine\ORM\Extension\DateFormat::class);
            $config->addCustomDatetimeFunction('DAYOFMONTH', \DV\Doctrine\ORM\Extension\DayOfMonth::class);
            $config->addCustomDatetimeFunction('MONTH', \DV\Doctrine\ORM\Extension\Month::class);
            $config->addCustomDatetimeFunction('YEAR', \DV\Doctrine\ORM\Extension\Year::class);
            $config->addCustomDatetimeFunction('DATEDIFF', \DV\Doctrine\ORM\Extension\DateDiff::class);
        }

        if(isset($options['jsonPlugin']))    {
            ##
            $this->registerJsonFunctions($this->em) ;
        }

        if (! $this->em->isOpen()) {
            $this->em = $this->em->create($this->em->getConnection() , $this->em->getConfiguration());
        }

		return  $this->em ;
	}

	static public function createEntityIdentifier($identifier=DOCTRINE_ORM_READ) : array
    {
        return ['entityIdentifier' => sprintf('doctrine.entity_manager.%s' , $identifier)] ;
    }

    public function registerJsonFunctions(EntityManager &$em) : void
    {
        $config = $em->getConnection()->getConfiguration() ;
        #$config->addCustomStringFunction(DqlFunctions\JsonAppend::FUNCTION_NAME, DqlFunctions\JsonArrayAppend::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonArray::FUNCTION_NAME, DqlFunctions\JsonArray::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonArrayAppend::FUNCTION_NAME, DqlFunctions\JsonArrayAppend::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonArrayInsert::FUNCTION_NAME, DqlFunctions\JsonArrayInsert::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonContains::FUNCTION_NAME, DqlFunctions\JsonContains::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonContainsPath::FUNCTION_NAME, DqlFunctions\JsonContainsPath::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonDepth::FUNCTION_NAME, DqlFunctions\JsonDepth::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonExtract::FUNCTION_NAME, DqlFunctions\JsonExtract::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonInsert::FUNCTION_NAME, DqlFunctions\JsonInsert::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonKeys::FUNCTION_NAME, DqlFunctions\JsonKeys::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonLength::FUNCTION_NAME, DqlFunctions\JsonLength::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonMerge::FUNCTION_NAME, DqlFunctions\JsonMerge::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonObject::FUNCTION_NAME, DqlFunctions\JsonObject::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonQuote::FUNCTION_NAME, DqlFunctions\JsonQuote::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonRemove::FUNCTION_NAME, DqlFunctions\JsonRemove::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonReplace::FUNCTION_NAME, DqlFunctions\JsonReplace::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonSearch::FUNCTION_NAME, DqlFunctions\JsonSearch::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonSet::FUNCTION_NAME, DqlFunctions\JsonSet::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonType::FUNCTION_NAME, DqlFunctions\JsonType::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonUnquote::FUNCTION_NAME, DqlFunctions\JsonUnquote::class) ;
        $config->addCustomStringFunction(DqlFunctions\JsonValid::FUNCTION_NAME, DqlFunctions\JsonValid::class) ;
    }

    /**
     * DOCTRINE ENTITY MANAGER
     * @var \Doctrine\ORM\EntityManager $platform
     */
    protected function registerDoctrineDatatypeExtras(EntityManager $em) : EntityManager
    {
        try{
            $conn = $em->getConnection();

            if(! $conn instanceof Connection)    {
                throw new RuntimeException('Bad Doctrine Connection Object') ;
            }

            $db_platform = $conn->getDatabasePlatform() ;
            $db_platform->registerDoctrineTypeMapping('enum', 'string') ;

            if(class_exists('DV\Doctrine\DBAL\Type\DateTimeToTimestampOutputType;'))    {
                ##
                Type::overrideType(DateTimeType::DATETIME , DateTimeToTimestampOutputType::class) ;
                $db_platform->registerDoctrineTypeMapping(DateTimeType::DATETIME, DateTimeType::DATETIME) ;
            }


            if(class_exists('DV\Doctrine\DBAL\Type\JsonType'))    {
                ## register my json that alwaz return config object
                Type::overrideType(\Doctrine\DBAL\Types\JsonType::JSON , JsonType::class) ;
                ##
                $db_platform->registerDoctrineTypeMapping(\Doctrine\DBAL\Types\JsonType::JSON , \Doctrine\DBAL\Types\JsonType::JSON) ;
            }


            if(class_exists('\Ramsey\Uuid\Doctrine\UuidType'))    {
                ##
                if(! Type::hasType(\Ramsey\Uuid\Doctrine\UuidType::NAME))    {
                    ##
                    Type::addType(\Ramsey\Uuid\Doctrine\UuidType::NAME , \Ramsey\Uuid\Doctrine\UuidType::class) ;
                }else{
                    Type::overrideType(\Ramsey\Uuid\Doctrine\UuidType::NAME , \Ramsey\Uuid\Doctrine\UuidType::class) ;
                }

                $db_platform->registerDoctrineTypeMapping(\Ramsey\Uuid\Doctrine\UuidType::NAME , \Ramsey\Uuid\Doctrine\UuidType::NAME) ;
            }

            ##
            return $em ;
        }
        catch (DBALException $ex)   {
            throw new RuntimeException( 'Trojan Datatype Error: Unable to process custom Database Type Mapping', 500 , $ex) ;
        }

    }

    /**
     *
     * @param string $_repository
     *
     * @return \Doctrine\ORM\EntityRepository ;
     */
	public function getDoctrineRepository($repository , $options=[]) : EntityRepository
	{
	    if(null == count($options))    {
            $options = self::createEntityIdentifier() ;
        }

		$_repository = $this->getDoctrineEntityManager($options) ;
		return $_repository->getRepository($repository) ;
	}
	
	
	public function setDoctrineEntityManager($em) : void
	{
		$this->em = $em ;
	}

	
	/**
	 * 
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilder($options=[])
	{
	    if(isset($options['em']))    {
            $em = $options['em'];
        }else{
	        $em = $this->getDoctrineEntityManager($options) ;
        }

		##
		return $em->createQueryBuilder() ;
	}
	
	/**
	 * return Collection criteria 
	 * 
	 * @return \Doctrine\Common\Collections\Criteria
	 */
	static public function criteria()
	{
		$criteria = Criteria::create() ;
		return $criteria;
	}
	
	/**
	 * return a criteria query expression
	 * 
	 * @return ExpressionBuilder
	 */
	static public function expr()
	{
		$expr = Criteria::expr() ;
		return $expr ;
	}

    /**
     *
     * @param string $service_name
     * @param string $params
     * @return  mixed|\Zend\ServiceManager\ServiceManager
     */
    public function getLocator($service_name)
    {
        if($container = $this->getContainer())  {
            return $container->get($service_name) ;
        }
        return ContainerFactory::getLocator($service_name) ;
    }

    protected function persist($entity)
    {
        $this->getDoctrineEntityManager()->persist($entity) ;
    }


    protected function flush()
    {
        $this->getDoctrineEntityManager()->flush();
    }

    public function getRepositoryName()
    {
        return $this->_repository_name ;
    }

    public function setRepositoryName($repository_name)
    {
        $this->_repository_name = $repository_name ;
    }

    protected function getEntityAlias()
    {
        return $this->_alias ;
    }

    protected function setEntityAlias($alias)
    {
        $this->_alias = $alias ;
    }


    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->getDoctrineEntityManager()->createQueryBuilder($alias) ;
    }

    public function setEntityParams(&$entity , $options)
    {
        ### iterate through the value that has been set in the $msg_data lambda
        foreach ($options as $column_key => $column_value)	{
            ### create a callable method in the message_entity
            $entity_method = 'set'.ucfirst($column_key) ;

            ### verify if a method is callable in the $message_entity class file
            if(is_callable([$entity , $entity_method]))	{
                ### set the value
                $entity->{$entity_method}($column_value) ;
            }
        }

        return $entity ;
    }

    public function getEntityParams($entity , &$r_params)
    {
        ###
        $_params = new Parameters() ;
        ###
        $entity_reflector = new ReflectionObject($entity) ;
        ###
        $entity_properties = $entity_reflector->getProperties(ReflectionProperty::IS_PRIVATE|ReflectionProperty::IS_PROTECTED
            |ReflectionProperty::IS_PUBLIC
        ) ;

        foreach ($entity_properties  as $_entity_var)		{
            ## set the property to be accessible
            $_entity_var->setAccessible(true);
            ###
            $_params[$_entity_var->getName()] = $_entity_var->getValue($entity) ;
        }

        if($r_params instanceof Parameters)	{
            $r_params->fromArray(array_merge($r_params->toArray() , $_params->toArray()));
        }
        else{
            $r_params = $_params ;
        }

        return $_params ;
    }

    public function getEntityAsArray($entity)
    {
        $entity_reflector = new ReflectionObject($entity) ;
        ###
        $entity_properties = $entity_reflector->getProperties(ReflectionProperty::IS_PRIVATE|ReflectionProperty::IS_PROTECTED
            |ReflectionProperty::IS_PUBLIC
        ) ;

        $_params = [] ;

        foreach ($entity_properties  as $_entity_var)		{
            ## set the property to be accessible
            $_entity_var->setAccessible(true);
            ###
            $_params[$_entity_var->getName()] = $_entity_var->getValue($entity) ;
        };

        return $_params ;
    }

    /**
     * Run a DQL query;
     *
     * @param $dql
     * @param null $max_results
     * @return \Doctrine\ORM\AbstractQuery ::getResult
     */
    public function runQuery($dql , $max_results=null)
    {
        $query = $this->getDoctrineEntityManager()->createQuery($dql) ;

        if(null != $dql)	{
            $query->setMaxResults($max_results) ;
        }

        return $query->getResult();
    }

    /**
     * Run a native SQL Query
     * @param array $_options
     * @throws \Exception
     * @return \Doctrine\DBAL\Connection
     */
    public function runNativeQuery($_options)
    {
        if(is_string($_options))	{
            $sql = $_options ;
        }

        ###
        if(! isset($_options['em']))	{
            $_options['em'] = $this->getDoctrineEntityManager() ;
        }
        /*
         * @var \Doctrine\ORM\EntityManager
         */
        $em = $_options['em'];

        if(is_array($_options))	{
            ###
            if(! isset($_options['sql']))	{
                throw new \Exception('sql statement key is not available') ;
            }
            $sql = $_options['sql'] ;
        }

        ###
        if(! isset($sql))	{
            throw new \Exception('sql statement to execute is not available') ;
        }

        ###
        if(! isset($_options['params']))	{
            $_options['params'] = null ;
        }
        $params = $_options['params'] ;

        ###
        $stmt = $em->getConnection() ;
        #$stmt = $this->get_doctrine_entity_manager()->getConnection() ;

        if(isset($_options['direct-execution']))	{
            ###
            return $stmt->query($sql)->fetchAll() ;
        }

        if(isset($_options['fetch']))	{
            ### return the result
            $stmt->prepare($sql)->execute($params) ;
            return $stmt->fetchAll();
        }

        return $stmt->prepare($sql)->execute($params) ;
    }
	
}