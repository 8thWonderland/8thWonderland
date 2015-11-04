<?php

namespace Wonderland\Library\Templating;

class Renderer {
    /** @var array **/
    protected $parameters;
    
    /**
     * @param string $view
     * @param array $parameters
     */
    public function render($view, $parameters) {
        $this->parameters = $parameters;
        
        require("{$this->rootPath}/Application/views/$view.view");
    }
}