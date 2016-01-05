<?php

namespace Wonderland\Library\Templating;

use Wonderland\Library\Translator;
use PhpAbac\Abac;

class Renderer
{
    /** @var array **/
    protected $parameters = [];
    /** @var string **/
    protected $rootPath;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    /** @var \PhpAbac\Abac **/
    protected $abac;

    /**
     * @param string                                    $rootPath
     * @param \Wonderland\Library\Templating\Translator $translator
     */
    public function __construct($rootPath, Translator $translator, Abac $abac)
    {
        $this->rootPath = $rootPath;
        $this->translator = $translator;
        $this->abac = $abac;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param array $parameters
     */
    public function addParameters($parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * @param type $key
     */
    public function removeParameter($key)
    {
        unset($this->parameters[$key]);
    }

    /**
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    public function render($view, $parameters = [])
    {
        if (count($parameters) > 0) {
            $this->addParameters($parameters);
        }
        ob_start();
        require "{$this->rootPath}/Application/views/$view.view";
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    /**
     * @param string $key
     */
    public function translate($key)
    {
        echo $this->translator->translate($key);
    }
    
    /**
     * @param string $key
     * @return string
     */
    public function getTranslation($key) {
        return $this->translator->translate($key);
    }

    /**
     * @param string $rule
     * @param int    $userId
     * @param int    $objectId
     * @param array  $dynamicAttributes
     *
     * @return bool
     */
    public function isEnforced($rule, $userId, $objectId = null, $dynamicAttributes = [])
    {
        return $this->abac->enforce($rule, $userId, $objectId, $dynamicAttributes) === true;
    }
}
