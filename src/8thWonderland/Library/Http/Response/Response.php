<?php

namespace Wonderland\Library\Http\Response;

class Response extends AbstractResponse {
    /**
     * @param string $data
     * @param int $status
     */
    public function __construct($data = '', $status = 200) {
        $this->data = $data;
        $this->status = $status;
    }
    
    public function makeHeaders() {
        header("{$_SERVER['SERVER_PROTOCOL']} {$this->status} {$this->reasons[$this->status]}");
    }
    
    public function respond() {
        echo $this->data;
    }
}