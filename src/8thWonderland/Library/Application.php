<?php

namespace Wonderland\Library;

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
        
        $containerData = json_decode(file_get_contents($this->rootPath.'Application/config/config.json'), true);
        
        $this->setServices($containerData['services']);
        $this->setParameters(array_merge($containerData['parameters'], ['root_path' => $this->rootPath]));
    }
    
    public function setServices($services) {
        reset($services);
        
        while($key = key($services)) {
            $service = $services[$key];
            $this->container[$key] = function($c) use ($service) {
                return
                    (new \ReflectionClass($service['class']))
                    ->newInstanceArgs($this->parseArguments($c, $service))
                ;
            };
            next($services);
        }
    }
    
    /**
     * @param array $service
     * @return array
     */
    public function parseArguments($container, $service) {
        $arguments = [];
        if(!isset($service['arguments'])) {
            return $arguments;
        }
        $nbArguments = count($service['arguments']);
        for($i = 0; $i < $nbArguments; ++$i) {
            $argument = $service['arguments'][$i];
            // retrieve the prefix to know which type of argument it is
            $argumentType = substr($argument, 0, 1);
            $arguments[] =
                ($argumentType === '@')
                // If this is a service, we cut the '@' prefix
                ? $container[substr($argument, 1)]
                // If this is a parameter, we cut the '%' delimiters
                : $container[substr($argument, 1, -1)]
            ;
        }
        return $arguments;
    }
    
    public function setParameters($parameters) {
        reset($parameters);
        
        while($key = key($parameters)) {
            $this->container[$key] = $parameters[$key];
            
            next($parameters);
        }
    }
    
    /**
     * Shortcut to get a container item
     * 
     * @param string $item
     * @return mixed
     */
    public function get($item) {
        return $this->container[$item];
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
        $this->rootPath = str_replace('\\', '/', dirname(__DIR__) . '\\');
        // Temporary fix
        define('VIEWS_PATH', $this->rootPath . 'Application/views/' );
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
            (new Config($this))
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
    
    /**
     * Démarrage de l'application
     */
    public function run() {
        $front = new FrontController($this);
        $front->dispatch();
    }
}