<?php

namespace Wonderland\Application\Repository;

use Wonderland\Library\Database\Mysqli;

abstract class AbstractRepository {
    /** @var \Wonderland\Library\Database\Mysqli **/
    protected $connection;
    
    /**
     * @param \Wonderland\Library\Database\Mysqli $connection
     */
    public function __construct(Mysqli $connection) {
        $this->connection = $connection;
    }
}