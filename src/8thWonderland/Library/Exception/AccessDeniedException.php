<?php

namespace Wonderland\Library\Exception;

class AccessDeniedException extends AbstractException {
    public function handle() {
        header("{$_SERVER['SERVER_PROTOCOL']} 401 Unauthorized");
        header('Content-Type: application/json');
        
        echo json_encode([
            'message' => 'You are not allowed to access this resource'
        ]);
    }
}