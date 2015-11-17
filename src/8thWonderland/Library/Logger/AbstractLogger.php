<?php

namespace Wonderland\Library\Logger;

abstract class AbstractLogger {
    /**
     * @param string $type
     * @param string $message
     */
    abstract public function log($type, $message);
}