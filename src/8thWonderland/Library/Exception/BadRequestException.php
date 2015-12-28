<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\JsonResponse;

class BadRequestException extends AbstractException {
    public function handle() {
        return new JsonResponse([
            'errors' => [
                $this->getMessage()
            ]
        ], 400);
    }
}