<?php

namespace Wonderland\Library\Exception;

use Wonderland\Library\Http\Response\JsonResponse;

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
        return new JsonResponse($this->rejectedAttributes, 403);
    }
    
    public function getRejectedAttributes() {
        return $this->rejectedAttributes;
    }
}