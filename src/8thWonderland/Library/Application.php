<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;
use Wonderland\Library\Database\Mysqli;
use Wonderland\Library\Controller\FrontController;
use Wonderland\Library\Config;

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
    
    /**
     * @param string $environment
     */
    public function __construct()
    {
        // Gestion des sessions - memory_registry
        Registry::start();
    }
    
    public function init($environment = 'production', $options = []) {
        $this->setRootPath();
        $this->setConfig($environment, $options);
        $this->setPlugins();
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
    

    // Initialisation des plugins (translate, db, logs, mail, etc .....)
    // =================================================================
    private function setPlugins()
    {
        Registry::set('db', new Mysqli());
        Registry::set('translate', new Translate($this->config->getOption('language')));
    }
    
    
    // Démarrage de l'application
    // ==========================
    public function run()
    {
        $front = new FrontController($this->config->getOptions());
        $front->dispatch();
    }
}