<?php

namespace Wonderland\Library\Http\Request;

use Wonderland\Library\Exception\BadRequestException;

class Request {
    /** @var array **/
    protected $headers;
    
    public function __construct() {
        $this->setHeaders();
    }
    
    public function setHeaders() {
        if(function_exists('getallheaders')) {
            $this->headers = getallheaders();
            return true;
        }
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $this->headers[strtolower(str_replace('_', '-', substr($name, 5)))] = $value;
            }
        }
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
}