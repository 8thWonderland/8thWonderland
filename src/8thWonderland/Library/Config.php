<?php

namespace Wonderland\Library;

/**
 * Description of config
 *
 * @author Brennan
 */
class Config {
    /** @var array **/
    protected $options = [];
    /** @var string **/
    protected $environment;
    protected $_keySeparator = ".";                                         // Séparateur à l'intérieur d'une clé
    protected $_sectionSeparator = ':';                                     // Séparateur à l'intérieur d'une section
    protected $_defaultSection;                                             // section par défaut définie dans l'application
    private $_iniArray;
    
    public function __construct()
    {
        $this->LoadIniFile();
    }

    /**
     * @param string $environment
     * @return \Wonderland\Library\Config
     */
    public function setEnvironment($environment) {
        $this->environment = $environment;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEnvironment() {
        return $this->environment;
    }

    public function LoadIniFile()
    {
        global $application;
        $filename = $application->getRootPath() . 'Application/config/application.ini';
        if (!file_exists($filename)) {
            throw new \Exception('The file ' . $filename . ' is not found !');
        }
        $loaded = parse_ini_file($filename, true);
        
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
        $this->options = $this->setSection($loaded);
    }
    
    /**
     * @param array $options
     * @return \Wonderland\Library\Config
     */
    public function setOptions($options) {
        $this->options[$this->environment] = array_merge($options, $this->options[$this->environment]);
        
        return $this;
    }
    
    /**
     * @param string $option
     * @param mixed $value
     * @return \Wonderland\Library\Config
     */
    public function setOption($option, $value) {
        $this->options[$this->environment][$option] = $value;
        
        return $this;
    }
    
    /**
     * @param string $option
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getOption($option) {
        if(!isset($this->options[$this->environment][$option])) {
            throw new \InvalidArgumentException("The requested option $option is not configured");
        }
        return $this->options[$this->environment][$option];
    }
    
    /**
     * @return array
     */
    public function getOptions() {
        return $this->options[$this->environment];
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
    
    /**
     * Transform keys to array if they comport the key separator
     * Recursive method
     * 
     * @param array $config
     * @param string $key
     * @param mixed $value
     * @return array
     * @throws \Exception
     */
    private function setKey($config, $key, $value)
    {
        if (strpos($key, $this->_keySeparator) === false) {
            $config[$key] = $value;
            return $config;
        }
        $pieces = explode($this->_keySeparator, $key, 2);

        // Vérifie l'existence de la clé
        if (empty($pieces[0]) || empty($pieces[1])) {
            throw new \Exception("Invalid key '" . $key . "'");
        }

        // Contrôle si la clé existe déjà
        if (!isset($config[$pieces[0]])) {
            $config[$pieces[0]] =
                ($pieces[0] === '0' && !empty($config))
                ? $config
                : []
            ;
        } elseif (!is_array($config[$pieces[0]])) {
            throw new \Exception("Cannot create sub-key for '{$pieces[0]}' as key already exists");
        }
        unset($config[$key]);
        $config[$pieces[0]] = $this->setKey($config[$pieces[0]], $pieces[1], $value);

        return $config;
    }

    /**
     * Format config to array
     * 
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->_iniArray as $key => $value) {
            $array[$key] =
                ($value instanceof Config)
                ? $value->toArray()
                : $value
            ;
        }
        return $array;
    }
}
