<?php


if(! defined('DV_DOCTRINE_INITIALIZED'))    {
    ##
    define('DV_DOCTRINE_INITIALIZED' , 1) ;
}

if(! defined('DV_DOCTRINE_ROOT'))    {
    ##
    define('DV_DOCTRINE_ROOT' , __DIR__) ;
}

if(! defined('DOCTRINE_ORM_READ'))    {
    ##
    define('DOCTRINE_ORM_READ' , 'orm_read') ;
}

if(! defined('DOCTRINE_ORM_WRITE'))    {
    ##
    define('DOCTRINE_ORM_WRITE' , 'orm_write') ;
}

if(! defined('DOCTRINE_ORM_CONFIG_FILE'))    {
    ##
    define('DOCTRINE_ORM_CONFIG_FILE' , DV_DOCTRINE_ROOT.'/config/doctrine.config.php') ;
}