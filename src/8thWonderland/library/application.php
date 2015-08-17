<?php

/**
 * Description of application
 *
 * @author Brennan
 */

class application {
    protected static $_instance;                // Instance unique de la classe
    protected $_environment;                    // Environnement de l'application
    protected $_autoloader;                     // Chargeur de classes
    public $_options = array();                 // Options de l'environnement
    
    
    // Initialisation de l'application
    // ===============================
    public function __construct($environment, $options = null)
    {
        // Définition des variables d'environnement
        $this->_environment = (string) $environment;

        
        // Chargement de l'autoloader
        require_once 'autoloader.php';
        $this->_autoloader = Autoloader::getInstance();
        
        
        // Gestion des sessions - memory_registry
        memory_registry::start();

        
        // Initialisation des options
        if (isset($options))                        {   $this->setOptions($options);                        }

    }
    
    
    // Mise en place du singleton
    // ==========================
    public static function getInstance($environment = null, $options = null)
    {
        if (null === self::$_instance) {
            if (isset($environment)) {
                self::$_instance = new self($environment, $options);
            } else {
                throw new Exception("Environment not defined !");
            }
        }
        return self::$_instance;
    }
    
    
    // Renvoi de la valeur d'une propriété
    // ===================================
    public function __get($property) 
    {
        if (ReflectionClass::hasProperty($property)) {
            return $this->$property;
        } else {
            throw new Exception('Invalid Property !');
        }
    }
    
    
    public static function getOptions()
    {
        return memory_registry::get('__options__');
    }
    
    
    // Initialisation des options
    // ==========================
    public function setOptions(&$options)
    {
        $opt = null;
        if (is_string($options)) {
            $o = config::getInstance($options, $this->_environment);
            $opt = $o->toArray();
        } elseif (is_array($options)) {
            $opt = $options;
        } else {
            throw new Exception('Invalid options provider : This must be a location of config file or an array !');
        }
        $cfg = array_change_key_case($opt[$this->_environment], CASE_LOWER);
        memory_registry::set('__options__', $cfg);

        
        
        $this->setIncludePaths();                               // paramétrage des chemin de type "includepath"
        
        if (!empty($cfg['phpsettings']))                        // paramétrage des variables PHP
        {   $this->setPhpSettings($cfg['phpsettings']);        }
        
        
        // paramétrage des plugins
        // =======================
        $this->setPlugins();
    }
    
    
    // Ajout de répertoires de recherche dans le registre de PHP
    // =========================================================
    private function setIncludePaths()
    {
        $cfg = memory_registry::get('__options__');
        if (!isset($cfg['includepaths']) || empty($cfg['includepaths']))        {   return;     }
        
        $path = implode(PATH_SEPARATOR, $cfg['includepaths']);
        set_include_path($path . PATH_SEPARATOR . get_include_path());
    }

    
    // Configuration des variables système de PHP
    // ==========================================
    private function setPhpSettings(array $settings, $prefix = '')
    {   
        foreach ($settings as $key => $value) {
            $key = empty($prefix) ? $key : $prefix . $key;
            if (is_scalar($value)) {
                ini_set($key, $value);
            } elseif (is_array($value)) {
                $this->setPhpSettings($value, $key . '.');
            }
        }

        return $this;
    }
    

    // Initialisation des plugins (translate, db, logs, mail, etc .....)
    // =================================================================
    private function setPlugins()
    {
        $cfg = memory_registry::get('__options__');
        
        // plugin de gestion de la base de données
        if (isset($cfg['db'])) {
            memory_registry::set('db', db_mysqli::getInstance());
        }
        
        // plugin d'internationalisation - translate
        if (!empty($cfg['language'])) {
            memory_registry::set('translate', translate::getInstance($cfg['language']));
        }
    }
    
    
    // Démarrage de l'application
    // ==========================
    public function run()
    {
	$cfg = memory_registry::get('__options__');
        
        $front = controllers_front::getInstance($cfg);
        $front->dispatch();
    }
}

?>
