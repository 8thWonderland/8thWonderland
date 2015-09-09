<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;
use Wonderland\Library\Database\Mysqli;
use Wonderland\Library\Controller\FrontController;
use Wonderland\Library\Config;

use Pimple\Container;

/**
 * Description of application
 *
 * @author Brennan
 */
class Application {
    /** @var string **/
    protected $rootPath;
    /** @var Config **/
    protected $config;
    /** @var Pimple\Container **/
    protected $container;
    
    public function init($environment = 'production', $options = []) {
        $this->setRootPath();
        $this->setContainer();
        $this->setConfig($environment, $options);
    }
    
    /**
     * Initialize the Pimple Container
     */
    public function setContainer() {
        $this->container = new Container();
        
        $containerData = json_decode(file_get_contents(PUBLIC_ROOT.'Application/config/config.json'), true);
        
        $this->setServices($containerData['services']);
        $this->setParameters($containerData['parameters']);
    }
    
    public function setServices($services) {
        reset($services);
        
        while($key = key($services)) {
            $service = $services[$key];
            $this->container[$key] = function($c) use ($service, $this) {
                return new $service['class']($this);
            };
            next($services);
        }
    }
    
    public function setParameters($parameters) {
        reset($parameters);
        
        while($key = key($parameters)) {
            $this->container[$key] = $parameters[$key];
            
            next($parameters);
        }
    }
    
    /**
     * @return \Pimple\Container
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * Set the absolute path to the project
     */
    public function setRootPath() {
        $this->rootPath = dirname(__DIR__) . '\\';
    }
    
    /**
     * Get the absolute path to the project
     * 
     * @return string
     */
    public function getRootPath() {
        return $this->rootPath;
    }
    
    /**
     * @param string $environment
     * @param array $options
     */
    public function setConfig($environment, $options = []) {
        $this->config =
            (new Config())
            ->setEnvironment($environment)
            ->setOptions($options)
        ;
    }
    
    /**
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }
    
    
    // Démarrage de l'application
    // ==========================
    public function run()
    {
        $front = new FrontController($this->config->getOptions());
        $front->dispatch();
    }
}