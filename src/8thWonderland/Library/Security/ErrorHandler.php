<?php

namespace Wonderland\Library\Security;

use Wonderland\Library\Logger\FileLogger;
use Wonderland\Library\Logger\DatabaseLogger;

class ErrorHandler {
    /** @var \Wonderland\Library\Logger\DatabaseLogger **/
    protected $databaseLogger;
    /** @var \Wonderland\Library\Logger\FileLogger **/
    protected $fileLogger;
    /** @var boolean **/
    protected $reporting;
    
    public function __construct(DatabaseLogger $databaseLogger, FileLogger $fileLogger, $reporting) {
        $this->databaseLogger = $databaseLogger;
        $this->fileLogger = $fileLogger;
        $this->reporting = $reporting;
    }
    
    /**
     * @param \Exception $exception
     */
    public function handleException(\Exception $exception) {
        if(is_a($exception, 'Wonderland\\Library\\Exception\\AbstractException')) {
            $exception->handle();
        }
        if($this->reporting !== false) {
            $this->logError(get_class($exception), "{$exception->getMessage()} at file {$exception->getFile()} at line {$exception->getLine()}");
        }
    }
    
    /**
     * @param int $id
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array $context
     */
    public function handleError($id, $message, $file = null, $line = null, $context = []) {
        $this->logError('error', $message);
    }
    
    public function logError($type, $message) {
        $this->databaseLogger->log($type, $message);
        $this->fileLogger->log($type, $message);
    }
}