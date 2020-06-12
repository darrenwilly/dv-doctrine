<?php
declare(strict_types=1);

namespace DV\Doctrine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use \Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql as DqlFunction ;

/**
 * This is the class that loads and manages DVDoctrineBundle configuration.
 *
 * @author DarrenTrojan <darren.willy@gmail.com>
 */
class DVDoctrineExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container): void
    {
        /**
         * make sure atleast one of the SCIENTA library class exist
         */
        if(! class_exists(\Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mysql\JsonSearch::class))    {
             return;
        }

        ## fetch all the extension
        $extension = $container->getExtensions() ;

        ##
        if (isset($extension['doctrine'])) {
            /**
             * load extra JSON Function lbrary and add it to ORM JSON String functions.
             * This logic will be enhance later to load all the file in the adapter directory and use the class name
             * to add them to string_functions
             */
            $ormConfig = [
                'orm' => [
                    'entity_managers' => [
                        'default' => [
                            'dql' => [
                                'string_functions' => [
                                    DqlFunction\JsonSearch::FUNCTION_NAME => DqlFunction\JsonSearch::class,
                                    DqlFunction\JsonContains::FUNCTION_NAME => DqlFunction\JsonContains::class,
                                    DqlFunction\JsonContainsPath::FUNCTION_NAME => DqlFunction\JsonContainsPath::class,
                                    DqlFunction\JsonExtract::FUNCTION_NAME => DqlFunction\JsonExtract::class,
                                    DqlFunction\JsonQuote::FUNCTION_NAME => DqlFunction\JsonQuote::class,
                                    DqlFunction\JsonUnquote::FUNCTION_NAME => DqlFunction\JsonUnquote::class,
                                ]
                            ]
                        ]
                    ]
                ]
            ] ;

            ##
            $container->prependExtensionConfig('doctrine', $ormConfig);

        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        if(! defined('DV_DOCTRINE_INITIALIZED'))    {
            ## load the bootstrap file
            $bootstrap = dirname(dirname(__DIR__)) . '/bootstrap.php' ;
            ##
            if(! file_exists($bootstrap))    {
                throw new \RuntimeException('bootstrap file is required to initialized the Bundle for interopability purpose') ;
            }
            ##
            require $bootstrap ;
        }

        try {
            ##
            $locator = new FileLocator(dirname(__DIR__) . '/Resources/config');

            ##
            $loader = new PhpFileLoader($container, $locator);
            ##
            $loader->load('services.php' , 'php');
            ##

        }
        catch (\Throwable $exception)   {
           # var_dump($exception->getMessage() . '<br>'. $exception->getTraceAsString()); exit;
        }
    }

}