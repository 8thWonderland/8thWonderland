<?php

namespace Wonderland\Library;

/**
 * Description of front
 *
 * @author Brennan
 */
class Front {
    protected static $_instance;                                            // Instance unique de la classe
    protected $_directory = 'application/controllers';                      // Chemin par défaut des controleurs action
    protected $_defaultController = 'index';                                // Nom du controleur par défaut
    protected $_defaultAction = 'index';                                    // Action par défaut


    public function __construct()
    {
        $options = memory_registry::get('__options__');
        $options = $options['frontcontroller'];
        
        if (isset($options['defaultController']))               {   $this->_defaultController = $options['defaultController'];   }
        if (isset($options['defaultAction']))                   {   $this->_defaultAction = $options['defaultAction'];   }
        if (isset($options['controllerDirectory']))             {   $this->_directory = $options['controllerDirectory'];   }
        if (!is_dir($this->_directory))         {   throw new exception("The default controllerDirectory '" . $this->_directory . "' is not exist !");  }

    }


    // Mise en place du singleton
    // ==========================
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    // Transfert de la requête au controleur d'action approprié
    // ========================================================
    public function dispatch()
    {
        // Détermination du controleur et de l'action pour cette requête
        $controller = $this->_defaultController;
        $action = $this->_defaultAction;

        if (isset($_REQUEST['controller']))    {   $controller=$_REQUEST['controller'];   }
        if (isset($_REQUEST['action']))        {   $action=$_REQUEST['action'];           }

        // Vérification si le controleur existe
        $filename = $this->_directory . "/" . $controller . ".php";
        if (!file_exists($filename))        {    throw new exception("The ActionController '" . $controller . "' does not exist !");  }

        // Vérification si l'action existe
        if (!in_array($action . "Action", get_class_methods($controller)))     {    throw new exception("The Action '" . $action . "' does not exist !");  }

        // route vers le controleur et l'action demandée
        $ctrl = new $controller();
        $action = $action . "Action";

        $ctrl->$action();
    }
}

?>
