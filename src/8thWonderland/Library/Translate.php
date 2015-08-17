<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;

/**
 * class translate
 *
 * Gestion de l'internationalisation du site
 *
 */
class Translate
{
    protected static $_instance;		// Singleton de la classe
    private $_langs = array();                  // Tableau des langues implémentées
    private $_defaultLang;                      // Langue par défaut de l'application si celle du navigateur n'est pas implémentée
    public $_langUser;                         // Langue définie par les membres quand ils sont connectés
    private $_langPath = 'langs/';              // Emplacement par défaut des fichiers de langues


    protected function __construct($options)
    {
        if (isset($options))
        {
            if (is_array($options))
            {
                if (!empty($options['path']) && is_dir($options['path']))       {   $this->_langPath = $options['path'];                }
                if (!empty($options['langs']))                                  {   $this->AddLang($options['langs']);                  }
                if (!empty($options['default']))                                {   $this->setDefault($options['default']);             }
            }
            else    {   throw new \Exception('Invalid options provider : This must be an array !');  }
        }
    }


    // Mise en place du singleton
    // ==========================
    public static function getInstance($options = null)
    {
        $res = Registry::get("__translate__");
        if (!isset($res)) {
            Registry::set("__translate__",  new self($options));
        }
        
        return Registry::get("__translate__");
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
    
    
    // Controle de la disponibilité d'une langue
    // =========================================
    public function isAvailable($lang)
    {
        if ($lang != null)   {   return (in_array($lang, $this->getList()));     }
        return false;
    }
    

    // Chargement du fichier de langue
    // ===============================
    protected function LoadFile($filename)
    {
        $array = null;
        // Controle la validité du fichier de langue
        // =========================================
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {
            throw new \Exception('filename incorrect !');
        }
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $this->_langPath . $filename)) {
            throw new \Exception('file "' . $_SERVER['DOCUMENT_ROOT'] . "/" . $this->_langPath . $filename . '" not exist !');
        }

        
        require_once $this->_langPath . $filename;
        return $array;
    }


    // Définition de la langue choisie par l'utilisateur
    // =================================================
    public function setLangUser($Lang)
    {
        if ($this->isAvailable($Lang))      {   $this->_langUser = $Lang;  }
        else                                {   throw new \Exception('Lang "' . $Lang . '" not implemented !');    }
    }
    
    
    // Définition de la langue par défaut de l'application
    // ===================================================
    public function setDefault($Lang)
    {
        if ($this->isAvailable($Lang))      {   $this->_defaultLang = $Lang;  }
        else                                {   throw new \Exception('Lang "' . $Lang . '" not implemented !');    }
    }


    // Récupération de la langue par défaut
    // ====================================
    public function getDefault()
    {
        return $this->_defaultLang;
    }


    // Récupération de la langue du navigateur
    // =======================================
    public function getLangBrowser()
    {
        $tmp = null;
        $lg_nav = '';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))    {   $tmp = $_SERVER['HTTP_ACCEPT_LANGUAGE'];    }
        $tmp = explode(',' , $tmp);
        $lg_nav = explode('-', $tmp[0]);
        if (!$this->isAvailable($lg_nav[0]))        {   return $this->getDefault();    }
        
        return $lg_nav[0];
    }
	

    public function msg($key, $lang=null)
    {
        if (!isset($lang) || !$this->isAvailable($lang))
        {
            if (isset($this->_langUser) && $this->isAvailable($this->_langUser))    {   $lang = $this->_langUser;           }
            else                                                                    {   $lang = $this->getLangBrowser();    }
        }

        return $this->_langs[$lang][$key];
    }
}

?>