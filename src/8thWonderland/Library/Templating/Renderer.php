<?php

namespace Wonderland\Library\Templating;

use Wonderland\Library\Translator;

class Renderer {
    /** @var array **/
    protected $parameters = [];
    /** @var string **/
    protected $rootPath;
    
    /**
     * @param string $rootPath
     * @param \Wonderland\Library\Templating\Translator $translator
     */
    public function __construct($rootPath, Translator $translator) {
        $this->rootPath = $rootPath;
        $this->parameters['translator'] = $translator;
    }
    
    /**
     * @param string $key
     * @param mixed $value
     */
    public function addParameter($key, $value) {
        $this->parameters[$key] = $value;
    }
    
    /**
     * @param array $parameters
     */
    public function addParameters($parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
    
    /**
     * @param type $key
     */
    public function removeParameter($key) {
        unset($this->parameters[$key]);
    }
    
    /**
     * @param string $view
     * @param array $parameters
     */
    public function render($view, $parameters = []) {
        if(count($parameters) > 0) {
            $this->addParameters($parameters);
        }
        require("{$this->rootPath}/Application/views/$view.view");
    }
}