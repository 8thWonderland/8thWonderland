<?php

namespace Wonderland\Library\Controller;

/**
 * Description of front
 *
 * @author Brennan
 */
class FrontController {
    protected static $_instance;                                            // Instance unique de la classe
    protected $_directory;                      // Chemin par défaut des controleurs action
    protected $_defaultController = 'Index';                                // Nom du controleur par défaut
    protected $_defaultAction = 'index';                                    // Action par défaut


    public function __construct($options = null)
    {
        $this->_directory = $_directory = APPLICATION_PATH . 'Controller';
        if (isset($options['defaultController']))               {   $this->_defaultController = $options['defaultController'];   }
        if (isset($options['defaultAction']))                   {   $this->_defaultAction = $options['defaultAction'];   }
        if (isset($options['controllerDirectory']))             {   $this->_directory = $options['controllerDirectory'];   }
        if (!is_dir($this->_directory))         {   throw new \Exception("The default controllerDirectory '" . $this->_directory . "' is not exist !");  }

    }


    // Mise en place du singleton
    // ==========================
    public static function getInstance($options = null)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($options);
        }
        return self::$_instance;
    }


    // Transfert de la requête au controleur d'action approprié
    // ========================================================
    public function dispatch()
    {
        // Détermination du controleur et de l'action pour cette requête
        $controller = 'Wonderland\\Application\\Controller\\' . $this->_defaultController . 'Controller';
        $action = $this->_defaultAction . 'Action';

        if (isset($_REQUEST['controller']))    {   $controller=$_REQUEST['controller'];   }
        if (isset($_REQUEST['action']))        {   $action=$_REQUEST['action'];           }

        // Vérification si le controleur existe
        if (!class_exists($controller)) {
            throw new \Exception("The ActionController '" . $controller . "' does not exist !");
        }
        // Vérification si l'action existe
        if (!method_exists($controller, $action)) {
            throw new \Exception("The Action '$action' does not exist !");
        }
        // route vers le controleur et l'action demandée
        $ctrl = new $controller();
        $ctrl->$action();
    }
}
