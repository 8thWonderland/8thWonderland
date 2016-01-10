<?php

namespace Wonderland\Application\Repository;

use Wonderland\Library\Database\PdoDriver;

abstract class AbstractRepository
{
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;

    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     */
    public function __construct(PdoDriver $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $min
     * @param int $max
     *
     * @return string
     */
    public function getRangeStatements($min = null, $max = null)
    {
        if ($min === null || $max === null) {
            return '';
        }

        return 'LIMIT '.($max - $min)." OFFSET $min";
    }
    
    public function beginTransaction() {
        if (!$this->connection->beginTransaction()) {
            $this->throwPdoException();
        }
    }
    
    public function commit() {
        if (!$this->connection->commit()) {
            $this->throwPdoException();
        }
    }
    
    /**
     * @param bool $throwException
     */
    public function rollback($throwException = false) {
        if (!$this->connection->rollBack()) {
            $this->throwPdoException();
        }
        if ($throwException) {
            $this->throwPdoException();
        }
    }
    
    /**
     * @throws \PDOException
     */
    public function throwPdoException() {
        throw new \PDOException(
            "[{$this->connection->errorCode()}] {$this->connection->errorInfo()[2]}"
        );
    }
}
