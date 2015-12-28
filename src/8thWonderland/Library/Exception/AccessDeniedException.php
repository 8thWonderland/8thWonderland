<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\JsonResponse;

class AccessDeniedException extends AbstractException {
    public function handle() {
        return new JsonResponse([
            'message' => 'You are not allowed to access this resource'
        ], 401);
    }
}