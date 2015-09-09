<?php

namespace Wonderland\Library\Controller;

use Wonderland\Library\Application;

class FrontController {
    /** @var \Wonderland\Library\Application */
    protected $application;
    /** @var string **/
    protected $directory;
    /** @var string **/
    protected $defaultController = 'Index';
    /** @var string **/
    protected $defaultAction = 'index';

    /**
     * @param Application $application
     * @throws \Exception
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->directory = $application->getRootPath() . 'Application/Controller';
        
        $options = $application->getConfig()->getOptions();
        
        if (isset($options['defaultController'])) {
            $this->defaultController = $options['defaultController'];
        }
        if (isset($options['defaultAction'])) {
            $this->defaultAction = $options['defaultAction'];
        }
        if (isset($options['controllerDirectory'])) {
            $this->directory = $options['controllerDirectory'];
        }
        if (!is_dir($this->directory)) {
            throw new \Exception("The default controllerDirectory '{$this->directory}' is not exist !");
        }
    }

    // Transfert de la requête au controleur d'action approprié
    // ========================================================
    public function dispatch()
    {
        // Détermination du controleur et de l'action pour cette requête
        $controller = 'Wonderland\\Application\\Controller\\';
        $controller .= 
            (isset($_REQUEST['controller']))
            ? $_REQUEST['controller']
            : $this->defaultController
        ;
        $controller .=  'Controller';
        
        $action =
            (isset($_REQUEST['action']))
            ? $_REQUEST['action']
            : $this->defaultAction
        ;
        $action .= 'Action';

        // Vérification si le controleur existe
        if (!class_exists($controller)) {
            throw new \Exception("The ActionController '" . $controller . "' does not exist !");
        }
        // Vérification si l'action existe
        if (!method_exists($controller, $action)) {
            throw new \Exception("The Action '$action' does not exist !");
        }
        (new $controller())->$action();
    }
}
