<?php

namespace Wonderland\Library\Http\Response;

class PaginatedResponse extends AbstractResponse {
    /** @var string **/
    protected $url;
    
    /**
     * @param mixed $data
     * @param string $rangeUnit
     * @param string $range
     * @param int $maxElements
     * @param int $status
     */
    public function __construct($data = '', $rangeUnit, $range, $maxElements, $status = 200) {
        $this->data = $data;
        $this->status = $status;
        $this->url = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
        
        $this->rangeUnit = $rangeUnit;
        $this->range = $range;
        $this->maxElements = $maxElements;
    }
    
    public function makeHeaders() {
        header("{$_SERVER['SERVER_PROTOCOL']} {$this->status} {$this->reasons[$this->status]}");
        header("Accept-Ranges: {$this->rangeUnit}");
        header("Content-Range: {$this->range}/{$this->maxElements}");
        header("Link: {$this->getLinks()}");
    }
    
    public function getLinks() {
        
    }
    
    public function respond() {
        
    }
}