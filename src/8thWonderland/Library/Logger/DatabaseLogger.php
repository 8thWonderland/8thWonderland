<?php

namespace Wonderland\Library\Logger;

use Wonderland\Library\Database\PdoDriver;

class DatabaseLogger extends AbstractLogger
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
     * {@inheritdoc}
     */
    public function log($type, $message)
    {
        $this->connection->prepareStatement(
            'INSERT INTO logs(type, message, created_at) VALUES(:type, :message, :created_at)', [
            'type' => $type,
            'message' => $message,
            'created_at' => (new \DateTime())->format('c'),
        ]);
    }
}
