<?php

/**
 * Description of autoloader
 *
 * @author Brennan
 */


class autoloader {
    protected static $_instance;
    
    
    protected function __construct()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
    
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    public static function Autoload($classname)
    {
        $filename = strtolower(str_replace("_", "/", $classname)) . ".php";
        
        if (!empty($filename))	{	require_once $filename;     }
    }
}

?>
