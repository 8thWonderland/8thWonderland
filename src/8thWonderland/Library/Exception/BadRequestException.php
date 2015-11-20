<?php

namespace Wonderland\Library\Exception;

class BadRequestException extends AbstractException {
    public function handle() {
        header("{$_SERVER['SERVER_PROTOCOL']} 400 Bad Request");
        header('Content-Type: application/json');
        
        echo json_encode([
            'errors' => [
                $this->getMessage()
            ]
        ]);
    }
}