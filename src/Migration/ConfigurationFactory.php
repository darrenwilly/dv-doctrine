<?php

namespace DV\Doctrine\Migration;

use ContainerInteropDoctrine\AbstractFactory;
use ContainerInteropDoctrine\ConnectionFactory;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Configuration;
use Psr\Container\ContainerInterface;

/**
 * @method Connection __invoke(ContainerInterface $container)
 */
class ConfigurationFactory extends AbstractFactory
{
    /**
     * @var bool
     */
    private static $areTypesRegistered = false;

    /**
     * {@inheritdoc}
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        ##
        $config = $this->retrieveConfig($container, $configKey, 'migrations_configuration');
        ## connection config
        #$connConfig = $this->retrieveConfig($container, $configKey, 'connection');

        $connection = $this->retrieveDependency(
            $container ,
            $configKey,
            'connection',
            ConnectionFactory::class
        );

        $configuration = new Configuration($connection);
        $configuration->setName($config['name']) ;
        $configuration->setMigrationsDirectory($config['directory']) ;
        $configuration->setMigrationsNamespace($config['namespace']) ;
        $configuration->setMigrationsTableName($config['table_name']) ;
        $configuration->setMigrationsColumnName($config['column_name']) ;
        $configuration->setMigrationsColumnLength($config['column_length']) ;
        $configuration->setMigrationsExecutedAtColumnName($config['executed_at_column_name']) ;
        $configuration->setMigrationsAreOrganizedByYearAndMonth($config['organize_migrations']) ;
        $configuration->setCustomTemplate($config['custom_template']) ;
        $configuration->setAllOrNothing($config['all_or_nothing']) ;

        return $configuration ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig($configKey)
    {
        return [
            'directory' => realpath(APPLICATION_PATH . '/shared-utilities/src/Migration'),
            'namespace' => '\\Shared\\Migration' ,
            'table_name' => 'tbl_system_migration_versions',
            'column_name' => 'version',
            'column_length' => 255,
            'executed_at_column_name' => 'executed_at',
            'name' => sprintf('%s Migrations' , PROJECT_NAME),
            # available in version >= 1.2. Possible values: "BY_YEAR", "BY_YEAR_AND_MONTH", false
            'organize_migrations' => false,
            # available in version >= 1.2. Path to your custom migrations template
            'custom_template' => '' ,
            'all_or_nothing' => false
        ];
    }

}
