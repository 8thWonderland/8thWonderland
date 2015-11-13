<?php

namespace Wonderland\Library\Exception;

abstract class AbstractException extends \Exception {
    abstract public function handle();
}