<?php

namespace Wonderland\Library\Http\Response;

class JsonResponse extends AbstractResponse {
    /**
     * @param mixed $data
     * @param int $status
     */
    public function __construct($data, $status = 200) {
        $this->data = $data;
        $this->status = $status;
    }
    
    public function makeHeaders() {
        header("{$_SERVER['SERVER_PROTOCOL']} {$this->status} {$this->reasons[$this->status]}");
        header('Content-Type: application/json');
    }
    
    public function respond() {
        echo json_encode($this->data);
    }
}