<?php

namespace Wonderland\Library\Http\Request;

use Wonderland\Library\Exception\BadRequestException;

class Request {
    /** @var array **/
    protected $headers;
    /** @var array **/
    protected $parameters;
    
    public function __construct() {
        $this->setHeaders();
        $this->setParameters();
    }
    
    public function setHeaders() {
        if(function_exists('getallheaders')) {
            $this->headers = getallheaders();
            return true;
        }
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        // Special case with the Content type header which is not prefixed with "HTTP_"
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $this->headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }
    }
    
    public function setParameters() {
        if(isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
            $this->parameters = $this->getJsonData();
            return;
        }
        $this->parameters = array_merge($_GET, $_POST);
    }
    
    /**
     * @return array
     */
    public function getJsonData() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header) {
        return $this->headers[strtolower($header)];
    }
    
    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * @return array
     */
    public function getRange($defaultNbElements) {
        if(!isset($this->headers['range'])) {
            return [
                'min' => 0,
                'max' => $defaultNbElements
            ];
        }
        $range = explode('-', $this->headers['range']);
        return [
            'min' => $range[0],
            'max' => $range[1]
        ];
    }
    
    /**
     * @param string $parameterName
     * @param mixed $defaultValue
     * @param string $cast
     * @return string
     */
    public function get($parameterName, $defaultValue = null, $cast = null) {
        if(!isset($this->parameters[$parameterName])) {
            if($defaultValue === null) {
                throw new BadRequestException("Missing $parameterName parameter");
            }
            return $defaultValue;
        }
        if($cast !== null) {
            settype($this->parameters[$parameterName], $cast);
        }
        return $this->parameters[$parameterName];
    }
}