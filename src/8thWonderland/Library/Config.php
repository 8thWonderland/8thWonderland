<?php

namespace Wonderland\Library;

/**
 * Description of config
 *
 * @author Brennan
 */
class Config {
    protected static $_instance;                                            // Instance unique de la classe
    protected $_keySeparator = ".";                                         // Séparateur à l'intérieur d'une clé
    protected $_sectionSeparator = ':';                                     // Séparateur à l'intérieur d'une section
    protected $_filename = '/src/8thWonderland/Application/config/application.ini';            // chemin du fichier de configuration par défaut
    protected $_defaultSection;                                             // section par défaut définie dans l'application
    private $_iniArray = array();
    
    
    public function __construct($filename, $defaultSection)
    {
        $this->_defaultSection = $defaultSection;
        if (isset($filename))   {   $this->_filename = $filename;   }
        $this->_iniArray = (array)$this->Load_INIFile();
    }
        
    
    // Mise en place du singleton
    // ==========================
    public static function getInstance($filename = null, $defaultSection = null)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($filename, $defaultSection);
        }
        return self::$_instance;
    }
    
    
    // Renvoi de la valeur correspondant à une clé du tableau
    // ======================================================
    public function __get($key)
    {
        $result = null;
        if (array_key_exists($key, $this->_iniArray[$this->_defaultSection])) {
            $result = $this->_iniArray[$this->_defaultSection][$key];
        }
        return $result;
    }


    // Charge le fichier ini dans un array()
    // =====================================
    public function Load_INIFile()
    {
        if (!file_exists($this->_filename)) {
            throw new \Exception('The file ' . $this->_filename . ' is not found !');
        }
        $loaded = parse_ini_file($this->_filename, true);
        
        // Gestion des clés multiples
        foreach ($loaded as $sectionName => $sectionData) {
            $datas = array();
            $keys = array_keys($sectionData);
            for ($i=0; $i<count($keys); $i++)
            {
                $datas = $this->setKey($datas, $keys[$i], $sectionData[$keys[$i]]);
                $loaded[$sectionName] = $datas;
            }
        }
        
        // Gestion des sections multiples (2 maxi)
        $loaded = $this->setSection($loaded);
        
        return $loaded;
    }
    
    
    // Transformation des sections en array si elles comportent le sectionSeparator
    // ============================================================================
    private function setSection($loaded)
    {
        $iniArray = array();
        foreach ($loaded as $key => $data)
        {
            $pieces = explode($this->_sectionSeparator, $key);
            $thisSection = strtolower(trim($pieces[0]));
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $iniArray[$thisSection][trim($pieces[1])] = $data;
                    break;

                default:
                    throw new \Exception("The section '" . $thisSection . ":" . trim($pieces[1]) . "' may not extended in " . $this->_filename);
            }
        }
        
        return $iniArray;
    }
    
    
    // Transformation des clés en array si elles comportent le keySeparator
    // ** Méthode Récursive **
    // ====================================================================
    private function setKey($config, $key, $value)
    {
        if (strpos($key, $this->_keySeparator) !== false) {
            $pieces = explode($this->_keySeparator, $key, 2);
            
            // Vérifie l'existence de la clé
            if (strlen($pieces[0]) && strlen($pieces[1])) {
                
                // Contrôle si la clé existe déjà
                if (!isset($config[$pieces[0]])) {
                    if ($pieces[0] === '0' && !empty($config)) {
                        // convert the current values in $config into an array
                        $config = array($pieces[0] => $config);
                    } else {
                        $config[$pieces[0]] = array();
                    }
                    
                } elseif (!is_array($config[$pieces[0]])) {
                    throw new Exception("Cannot create sub-key for '" . $pieces[0] . "' as key already exists");
                }
                unset($config[$key]);
                $config[$pieces[0]] = $this->setKey($config[$pieces[0]], $pieces[1], $value);
                
            } else {
                throw new Exception("Invalid key '" . $key . "'");
            }
            
        } else {
            $config[$key] = $value;
        }

        return $config;
    }

    
    // renvoi des données sous la forme d'un tableau
    // =============================================
    public function toArray()
    {
        $array = array();
        $data = $this->_iniArray;
        foreach ($data as $key => $value) {
            if ($value instanceof Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }
}

?>
