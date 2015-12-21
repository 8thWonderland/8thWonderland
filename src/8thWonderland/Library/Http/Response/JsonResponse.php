<?php

namespace Wonderland\Library\Http\Response;

class JsonResponse extends AbstractResponse {
    /** @var array */
    protected $primitiveHeaders = [
        'Content-Type' => 'application/json'
    ];
    
    /**
     * @param mixed $data
     * @param int $status
     */
    public function __construct($data, $status = 200) {
        $this->data = $data;
        $this->status = $status;
    }
    
    public function respond() {
        echo json_encode($this->data);
    }
}