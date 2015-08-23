<?php

namespace Wonderland\Library;

/**
 * class translate
 *
 * Gestion de l'internationalisation du site
 *
 */
class Translate
{
    private $_langs = array();                  // Tableau des langues implémentées
    private $_defaultLang;                      // Langue par défaut de l'application si celle du navigateur n'est pas implémentée
    public $_langUser;                         // Langue définie par les membres quand ils sont connectés
    private $_langPath = 'langs/';              // Emplacement par défaut des fichiers de langues

    public function __construct($options = [])
    {
        if (!is_array($options))
        {
            throw new \InvalidArgumentException('Invalid options provider : This must be an array !');
        }
        if (!empty($options['path']) && is_dir($options['path'])) {
            $this->_langPath = $options['path'];
        }
        if (!empty($options['langs'])) {
            $this->AddLang($options['langs']);
        }
        if (!empty($options['default'])) {
            $this->setDefault($options['default']);
        }
    }

    // Ajout d'une langue si elle n'existe pas déjà
    // ============================================
    public function AddLang(array $langs)
    {
        foreach($langs as $name => $file)
        {
            if (!array_key_exists($name, $this->_langs))    {   $this->_langs[$name] = self::LoadFile($file);   }
        }
    }
    
    
    // Suppression d'une langue
    // ========================
    public function RemoveLang($name)
    {
        if (array_key_exists($name, $this->_langs)) {
            unset($this->_langs[$name]);
        }
    }
    
    
    // Retourne la liste des langues implémentées
    // ==========================================
    public function getList()
    {
        return array_keys($this->_langs);
    }
    
    /**
     * @param string $language
     * @return boolean
     */
    public function isAvailable($language)
    {
        if ($language != null) {
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
        global $application;
        $array = null;
        // Controle la validité du fichier de langue
        // =========================================
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            throw new \InvalidArgumentException('filename incorrect !');
        }
        if(!file_exists($application->getRootPath() . $this->_langPath . $filename)) {
            throw new \InvalidArgumentException('file "' . $application->getRootPath() . $this->_langPath . $filename . '" not exist !');
        }
        require_once $application->getRootPath() . $this->_langPath . $filename;
        return $array;
    }

    /**
     * @param string $language
     * @throws \InvalidArgumentException
     */
    public function setLangUser($language)
    {
        if (!$this->isAvailable($language)) {
            throw new \InvalidArgumentException('Language "' . $language . '" not implemented !');
        }
        $this->_langUser = $language;
    }
    
    /**
     * @param string $language
     * @throws \InvalidArgumentException
     */
    public function setDefault($language) {
        if (!$this->isAvailable($language)) {
            throw new \InvalidArgumentException('Language "' . $language . '" not implemented !');
        }
        $this->_defaultLang = $language;
    }


    // Récupération de la langue par défaut
    // ====================================
    public function getDefault()
    {
        return $this->_defaultLang;
    }


    // Récupération de la langue du navigateur
    // =======================================
    public function getBrowserLang()
    {
        $tmp = null;
        $lg_nav = '';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $tmp = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        $tmp = explode(',' , $tmp);
        $lg_nav = explode('-', $tmp[0]);
        if (!$this->isAvailable($lg_nav[0])) {
            return $this->getDefault();
        }
        return $lg_nav[0];
    }
	
    /**
     * @param string $key
     * @param string $lang
     * @return string
     */
    public function msg($key, $lang=null)
    {
        if (!isset($lang) || !$this->isAvailable($lang))
        {
            $lang = 
                (isset($this->_langUser) && $this->isAvailable($this->_langUser))
                ? $this->_langUser
                : $this->getBrowserLang()
            ;
        }
        return $this->_langs[$lang][$key];
    }
}
