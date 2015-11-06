<?php

namespace Wonderland\Library\Controller;

use Wonderland\Library\Application;

abstract class ActionController {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    /** @var string **/
    protected $controllersDirectory = 'src/8thWonderland/Application/Controller';
    /** @var \Wonderland\Application\Model\Member **/
    protected $user;
    
    /**
     * @param Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param string $view
     * @param array $parameters
     * @param array $headers
     */
    public function render($view, $parameters = [], $headers = []) {
        $this->application->get('templating')->render($view, $parameters, $headers);
    }
    
    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getUser() {
        if($this->user === null) {
            $this->user = $this
                ->application
                ->get('member_manager')
                ->getMember($this->application->get('session')->get('__id__'))
            ;
        }
        return $this->user;
    }
    
    // Affichage d'un texte directement
    // SHAME
    // @ToRemove !!!
    // ================================
    public function display($msg) {
        echo $msg;
    }
    
    /**
     * @param string $url
     */
    public function redirect($url)
    {   
        if ($this->is_Ajax()) {
            echo json_encode([
                'status' => 1,
                'response' => $url
            ]);
        } else {
            $params = $this->_formatURL($url);

            // route vers le controleur et l'action demandée
            $ctrl = new $params[0]($this->application);
            $action = $params[1] . "Action";

            $ctrl->$action();
        }
        exit();
    }
    
    /**
     * @return boolean
     */
    protected function is_Ajax()
    {
        return (
            array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
    
    /**
     * @param string $url
     * @return array
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function _formatURL($url)
    {
        $action = 'index';
        if ($url[0] != '/') {
            $url = '/'.$url;
        }
        $params = explode('/', $url);
        if (count($params) < 2) {
            throw new \InvalidArgumentException('The url is invalid !');
        }
        $controller = "Wonderland\\Application\\Controller\\{$params[1]}Controller";
        if (isset($params[2])) {
            $action = $params[2];
        }
        
        // Vérification si le controleur existe
        $filename = "{$this->controllersDirectory}/{$params[1]}Controller.php";
        if (!file_exists($filename)) {
            throw new \Exception("The ActionController '$controller' does not exist !");
        }
        // Vérification si l'action existe
        if (!in_array("{$action}Action", get_class_methods($controller))) {
            throw new \Exception("The Action '$action' does not exist !");
        }
        return [$controller, $action];
    }
}