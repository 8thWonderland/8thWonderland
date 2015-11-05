<?php

namespace Wonderland\Library\Templating;

use Wonderland\Library\Translator;

class Renderer {
    /** @var array **/
    protected $parameters = [];
    /** @var string **/
    protected $rootPath;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    
    /**
     * @param string $rootPath
     * @param \Wonderland\Library\Templating\Translator $translator
     */
    public function __construct($rootPath, Translator $translator) {
        $this->rootPath = $rootPath;
        $this->translator = $translator;
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
    
    /**
     * @param string $key
     */
    public function translate($key) {
        echo $this->translator->translate($key);
    }
}