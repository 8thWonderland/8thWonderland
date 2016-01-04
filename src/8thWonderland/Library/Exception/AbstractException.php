<?php

namespace Wonderland\Library\Exception;

abstract class AbstractException extends \Exception
{
    /**
     * @return \Wonderland\Library\Http\Response\AbstractResponse
     */
    abstract public function handle();
}
