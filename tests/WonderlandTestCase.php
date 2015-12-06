<?php

namespace Wonderland\Test;

use Wonderland\Library\Database\PdoDriver;

class WonderlandTestCase extends \PHPUnit_Framework_TestCase {
    /** @var \PDO */
    private $connection;

    /**
     * @param string $fixture
     * @throws \InvalidArgumentException
     */
    public function loadFixture($fixture) {
        if (!is_file(__DIR__ . "/fixtures/$fixture.sql")) {
            throw new \InvalidArgumentException("The asked fixture file $fixture.sql does not exists");
        }
        $success = $this->getConnection()->exec(file_get_contents(__DIR__ . "/fixtures/$fixture.sql"));
        
        if($success === false) {
            $this->fail("Error with $fixture fixtures : {$this->connection->errorInfo()[2]}");
        }
    }

    /**
     * @return \PDO
     */
    protected function getConnection() {
        if (empty($this->connection)) {
            $this->connection = new PdoDriver('sqlite::memory:', null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        }
        return $this->connection;
    }
}