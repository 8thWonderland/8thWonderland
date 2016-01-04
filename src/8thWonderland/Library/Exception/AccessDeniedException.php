<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\Response;

class AccessDeniedException extends AbstractException
{
    public function handle()
    {
        return new Response('You are not authenticated', 401);
    }
}
