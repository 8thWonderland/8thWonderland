<?php

namespace Wonderland\Library;

class Translator {
    /** @var string **/
    protected $rootPath;
    /** @var array **/
    protected $languages = [];
    /** @var string **/
    protected $defaultLanguage;
    /** @var string **/
    protected $userLanguage;
    /** @var string **/
    protected $languagesPath = 'langs/';

    /**
     * @param string $rootPath
     * @param array $options
     */
    public function __construct($rootPath, $options) {
        $this->rootPath = $rootPath;
        
        if (!empty($options['path']) && is_dir($options['path'])) {
            $this->languagesPath = $options['path'];
        }
        if (!empty($options['langs'])) {
            $this->addLanguage($options['langs']);
        }
        if (!empty($options['default'])) {
            $this->setDefaultLanguage($options['default']);
        }
    }

    /**
     * @param array $langs
     */
    public function addLanguage(array $langs)
    {
        foreach($langs as $name => $file) {
            if (!array_key_exists($name, $this->languages)) {
                $this->languages[$name] = self::LoadFile($file);
            }
        }
    }
    
    
    // Suppression d'une langue
    // ========================
    public function removeLanguage($name)
    {
        if (array_key_exists($name, $this->languages)) {
            unset($this->languages[$name]);
        }
    }
    
    
    // Retourne la liste des langues implémentées
    // ==========================================
    public function getList()
    {
        return array_keys($this->languages);
    }
    
    /**
     * @param string $language
     * @return boolean
     */
    public function isAvailable($language)
    {
        if ($language !== null) {
            return (in_array($language, $this->getList()));
        }
        return false;
    }
    
    /**
     * @param string $filename
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function LoadFile($filename)
    {
        $array = null;
        // Controle la validité du fichier de langue
        // =========================================
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            throw new \InvalidArgumentException('filename incorrect !');
        }
        if(!file_exists($this->rootPath . $this->languagesPath . $filename)) {
            throw new \InvalidArgumentException('file "' . $this->rootPath . $this->languagesPath . $filename . '" not exist !');
        }
        require_once $this->rootPath . $this->languagesPath . $filename;
        return $array;
    }

    /**
     * @param string $language
     * @throws \InvalidArgumentException
     */
    public function setUserLang($language) {
        if (!$this->isAvailable($language)) {
            throw new \InvalidArgumentException("Language $language is not implemented !");
        }
        $this->userLanguage = $language;
    }
    
    /**
     * @param string $language
     * @throws \InvalidArgumentException
     */
    public function setDefaultLanguage($language) {
        if (!$this->isAvailable($language)) {
            throw new \InvalidArgumentException("Language $language is not implemented !");
        }
        $this->defaultLanguage = $language;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @return string
     */
    public function getBrowserLang()
    {
        $tmp = null;
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $tmp = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        $tmp = explode(',' , $tmp);
        $lg_nav = explode('-', $tmp[0]);
        if (!$this->isAvailable($lg_nav[0])) {
            return $this->getDefaultLanguage();
        }
        return $lg_nav[0];
    }
	
    /**
     * @param string $key
     * @param string $lang
     * @return string
     */
    public function translate($key, $lang = null) {
        if ($lang === null || !$this->isAvailable($lang)) {
            $lang = 
                ($this->userLanguage === null || !$this->isAvailable($this->userLanguage))
                ? $this->getBrowserLang()
                : $this->userLanguage
            ;
        }
        return 
            (isset($this->languages[$lang][$key]))
            ? $this->languages[$lang][$key]
            : $key
        ;
    }
}
