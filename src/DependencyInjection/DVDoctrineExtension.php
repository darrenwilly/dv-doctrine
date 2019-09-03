<?php
declare(strict_types=1);

namespace DV\Doctrine\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * This is the class that loads and manages DVDoctrineBundle configuration.
 *
 * @author DarrenTrojan <darren.willy@gmail.com>
 */
class DVDoctrineExtension extends Extension implements PrependExtensionInterface
{

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        if(! defined('DV_DOCTRINE_INITIALIZED'))    {
            ## load the bootstrap file
            $bootstrap = dirname(dirname(__DIR__)) . '/boostrap.php' ;
            ##
            if(! file_exists($bootstrap))    {
                throw new \RuntimeException('bootstrap file is required to initialized the Bundle for interopability purpose') ;
            }
            ##
            require $bootstrap ;
        }

        try {
            ##
            $locator = new FileLocator(dirname(__DIR__) . '/Resources/config/');
            ##
            $loader = new PhpFileLoader($container, $locator);
            ##
            $loader->load('services.php' , 'php');
            ##

        }
        catch (\Throwable $exception)   {
            var_dump($exception->getMessage() . '<br>'. $exception->getTraceAsString()); exit;
        }
    }

    /**
     * Ability to overide previous bundle configuration
     *
     * https://symfony.com/doc/4.1/bundles/prepend_extension.html
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');
    }
}