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
        $this->headers = getallheaders();
    }
    
    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header) {
        return $this->headers[$header];
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
    public function getRange() {
        if(!isset($this->headers['Range'])) {
            throw new BadRequestException('The range header must be set');
        }
        
        $range = explode('-', $this->headers['Range']);
        return [
            'min' => $range[0],
            'max' => $range[1]
        ];
    }
}