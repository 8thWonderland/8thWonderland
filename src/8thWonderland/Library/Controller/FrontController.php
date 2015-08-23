<?php

namespace Wonderland\Library\Controller;

/**
 * Description of front
 *
 * @author Brennan
 */
class FrontController {
    protected $_directory;                      // Chemin par défaut des controleurs action
    protected $_defaultController = 'Index';                                // Nom du controleur par défaut
    protected $_defaultAction = 'index';                                    // Action par défaut


    public function __construct($options = null)
    {
        global $application;
        $this->_directory = $application->getRootPath() . 'Application/Controller';
        if (isset($options['defaultController']))               {   $this->_defaultController = $options['defaultController'];   }
        if (isset($options['defaultAction']))                   {   $this->_defaultAction = $options['defaultAction'];   }
        if (isset($options['controllerDirectory']))             {   $this->_directory = $options['controllerDirectory'];   }
        if (!is_dir($this->_directory))         {   throw new \Exception("The default controllerDirectory '" . $this->_directory . "' is not exist !");  }

    }

    // Transfert de la requête au controleur d'action approprié
    // ========================================================
    public function dispatch()
    {
        // Détermination du controleur et de l'action pour cette requête

        $requestedController = (isset($_REQUEST['controller'])) ? $_REQUEST['controller'] : $this->_defaultController;
        $controller = 'Wonderland\\Application\\Controller\\' . $requestedController . 'Controller';
        
        $requestedAction = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : $this->_defaultAction;
        $action = $requestedAction . 'Action';

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
