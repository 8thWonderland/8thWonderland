<?php

namespace Wonderland\Application\Repository;

use Wonderland\Library\Application;

abstract class AbstractRepository {
    /** @var \Wonderland\Library\Database\Mysqli **/
    protected $connection;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->connection = $application->get('mysqli');
    }
}