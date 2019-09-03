<?php
namespace DV\Doctrine ;

use DV\TraitExceptionBase;
use RuntimeException as parentException ;
use Throwable;

class RuntimeException extends parentException
{
    use TraitExceptionBase;

    protected $logicIdentifier = __CLASS__ ;

    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        $finalMessage = $this->processMessage($message) ;
        ##
        parent::__construct($finalMessage, $code, $previous);
    }
}