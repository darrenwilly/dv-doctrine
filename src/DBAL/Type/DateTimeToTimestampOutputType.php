<?php
namespace DV\Doctrine\DBAL\Type;

use Carbon\Carbon;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use DV\Doctrine\RuntimeException;

class DateTimeToTimestampOutputType extends DateTimeType
{

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $valueFromParent = parent::convertToPHPValue($value , $platform) ;
        ##
        /*if($valueFromParent instanceof \DateTimeInterface)    {
            return $valueFromParent->getTimestamp() ;
        }*/

        return $valueFromParent ;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        ## check for timestamp value
        if(is_string($value) || is_numeric($value) || is_integer($value))    {
            ## if the length is eleven characters
            if(11 == strlen($value))    {
                $value = Carbon::createFromTimestamp($value) ;
            }
            else{
                $value = Carbon::createFromTimestampMs($value) ;
            }

            if(! $value)    {
                throw new RuntimeException(sprintf('Cannot identify nor convert the value assigned %s' , gettype($value)));
            }
        }
        return parent::convertToDatabaseValue($value , $platform) ;
    }
}