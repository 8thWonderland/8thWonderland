<?php

namespace Wonderland\Library\Logger;

class FileLogger extends AbstractLogger {
    /** @var string **/
    protected $rootPath;
    
    /**
     * @param string $rootPath
     */
    public function __construct($rootPath) {
        $this->rootPath = $rootPath;
    }
    
    /**
     * {@inheritdoc}
     */
    public function log($type, $message) {
        $datetime = new \DateTime();
        
        file_put_contents(
            "{$this->rootPath}/logs/{$datetime->format('d-m-Y')}.log",
            "[{$datetime->format('d-m-Y H:i:s')}] " . strtoupper($type). " - $message\n",
            FILE_APPEND
        );
    }
}