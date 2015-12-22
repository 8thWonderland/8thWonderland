<?php

namespace Wonderland\Library\Controller;

use Wonderland\Library\Application;

use Wonderland\Library\Exception\AccessDeniedException;
use Wonderland\Library\Exception\BadRequestException;
use Wonderland\Library\Exception\ForbiddenException;

use Wonderland\Library\Http\Response\Response;
use Wonderland\Library\Http\Response\RedirectResponse;

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
     * @return \Wonderland\Library\Http\Response\Response
     */
    public function render($view, $parameters = [], $headers = [], $status = 200) {
        return new Response($this
            ->application
            ->get('templating')
            ->render($view, $parameters)
        , $status);
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
    
    /**
     * @return array
     */
    public function getJsonRequest() {
        if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new BadRequestException('JSON data is expected');
        }
        return json_decode(file_get_contents('php://input'), true);
    }
    
    /**
     * @param string $url
     * @return \Wonderland\Library\Http\Response\AbstractResponse
     */
    public function redirect($url) {   
        if ($this->is_Ajax()) {
            $rootPath = 
                (!isset($_SERVER['BASE']))
                ? '/'
                : $_SERVER['BASE'] . '/'
            ;
            return new RedirectResponse($url, 301);
        }
        $params = $this->_formatURL($url);

        // route vers le controleur et l'action demandée
        $controller = new $params[0]($this->application);
        $action = "{$params[1]}Action";

        return $controller->$action();
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
        $controllerName = ucfirst($params[1]) . 'Controller';
        $controller = "Wonderland\\Application\\Controller\\$controllerName";
        if (isset($params[2])) {
            $action = $params[2];
        }
        
        // Vérification si le controleur existe
        $filename = "{$this->controllersDirectory}/$controllerName.php";
        if (!file_exists($filename)) {
            throw new \Exception("The ActionController '$controller' does not exist !");
        }
        // Vérification si l'action existe
        if (!in_array("{$action}Action", get_class_methods($controller))) {
            throw new \Exception("The Action '$action' does not exist !");
        }
        return [$controller, $action];
    }
    
    /**
     * @param string $rule
     * @param int $objectId
     * @param array $dynamicAttributes
     * @throws \Wonderland\Library\Exception\AccessDeniedException
     * @throws ForbiddenException
     */
    public function checkAccess($rule, $objectId = null, $dynamicAttributes = []) {
        if(($user = $this->getUser()) === null ) {
            throw new AccessDeniedException();
        }
        if(($check = $this->application->get('abac')->enforce($rule, $user->getId(), $objectId, $dynamicAttributes)) !== true) {
            throw new ForbiddenException($check);
        }
    }
}