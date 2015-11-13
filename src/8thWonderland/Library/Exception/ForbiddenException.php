<?php

namespace Wonderland\Library\Exception;

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
        header("{$_SERVER['SERVER_PROTOCOL']} 403 Forbidden");
    }
}