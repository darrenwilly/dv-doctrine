<?php
declare(strict_types=1);

namespace DV\Doctrine;


class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke() : array
    {
        $config = [
            'dependencies' => $this->getDependencies(),
        ];
        ##
        /*if($internalConfig = $this->getConfig())    {
            ##
            $config = array_merge_recursive($config , $internalConfig) ;
        }*/

        ##
        return $config ;
    }

    public function getConfig()
    {
        return require __DIR__ . '/../config/doctrine.config.php' ;
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies()
    {
        return [
            'aliases' => [

            ] ,
            'factories'  => [

            ],
        ];
    }

}
