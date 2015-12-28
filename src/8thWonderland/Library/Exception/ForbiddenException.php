<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\Response;

class ForbiddenException extends AbstractException {
    /** @var array **/
    protected $rejectedAttributes;
    
    /**
     * @param array $rejectedAttributes
     */
    public function __construct($rejectedAttributes) {
        $this->rejectedAttributes = $rejectedAttributes;
    }
    
    public function handle() {
        return new Response(null, 403);
    }
}