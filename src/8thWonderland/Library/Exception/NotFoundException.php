<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\Response;

class NotFoundException extends AbstractException {
    public function handle() {
        return new Response(null, 404);
    }
}