<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;
use Wonderland\Library\Database\Mysqli;
use Wonderland\Library\Controller\FrontController;
use Wonderland\Library\Config;

/**
 * Description of application
 *
 * @author Brennan
 */
class Application {
    /** @var Application **/
    protected static $_instance;
    /** @var string **/
    protected $_environment;
    /** @var array **/
    protected $_options = array();
    
    // Initialisation de l'application
    // ===============================
    public function __construct($environment, $options = null)
    {
        // Définition des variables d'environnement
        $this->_environment = (string) $environment;
        
        // Gestion des sessions - memory_registry
        Registry::start();

        // Initialisation des options
        if (isset($options)) {
            $this->setOptions($options);
        }
    }
    
    
    // Mise en place du singleton
    // ==========================
    public static function getInstance($environment = null, $options = null)
    {
        if (null === self::$_instance) {
            if (isset($environment)) {
                self::$_instance = new self($environment, $options);
            } else {
                throw new \Exception("Environment not defined !");
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
            throw new \Exception('Invalid Property !');
        }
    }
    
    
    public static function getOptions()
    {
        return Registry::get('__options__');
    }
    
    
    // Initialisation des options
    // ==========================
    public function setOptions(&$options)
    {
        $opt = null;
        if (is_string($options)) {
            $o = Config::getInstance($options, $this->_environment);
            $opt = $o->toArray();
        } elseif (is_array($options)) {
            $opt = $options;
        } else {
            throw new \Exception('Invalid options provider : This must be a location of config file or an array !');
        }
        $cfg = array_change_key_case($opt[$this->_environment], CASE_LOWER);
        Registry::set('__options__', $cfg);

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
        $cfg = Registry::get('__options__');
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
        $cfg = Registry::get('__options__');
        
        // plugin de gestion de la base de données
        if (isset($cfg['db'])) {
            Registry::set('db', Mysqli::getInstance());
        }
        
        // plugin d'internationalisation - translate
        if (!empty($cfg['language'])) {
            Registry::set('translate', Translate::getInstance($cfg['language']));
        }
    }
    
    
    // Démarrage de l'application
    // ==========================
    public function run()
    {
	$cfg = Registry::get('__options__');
        
        $front = FrontController::getInstance($cfg);
        $front->dispatch();
    }
}