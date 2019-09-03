<?php
namespace DV\Doctrine\DBAL\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType as parentClass;
use DV\Doctrine\RuntimeException;
use DV\Config\Config;

class JsonType extends parentClass
{

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        try{
            ##
            $parentValue = (array) parent::convertToPHPValue($value , $platform) ;
            ##
            if(class_exists(Config::class)) {
                ##
                return new Config($parentValue , true) ;
            }
            elseif (class_exists(ArrayCollection::class))   {
                ##
                return new ArrayCollection($parentValue) ;
            }
        }
        catch (\Exception $exception)   {
            ##
            throw new RuntimeException($exception->getMessage(), 500, $exception ) ;
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        try{
            ##
            if($value instanceof \Zend\Config\Config) {
                ##
                $value = $value->toArray() ;
            }
            elseif ($value instanceof ArrayCollection)   {
                ##
                $value = $value->toArray();
            }
            return parent::convertToDatabaseValue($value , $platform) ;
        }
        catch (\Exception $exception)   {
            ##
            throw new RuntimeException($exception->getMessage(), 500, $exception ) ;
        }
    }

}