<?php

namespace Wonderland\Application\Repository;

use Wonderland\Library\Database\PdoDriver;

abstract class AbstractRepository {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     */
    public function __construct(PdoDriver $connection) {
        $this->connection = $connection;
    }
}