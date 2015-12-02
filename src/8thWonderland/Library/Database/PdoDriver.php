<?php

namespace Wonderland\Library\Database;

class PdoDriver extends \PDO {
    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct($dsn, $username = null, $password = null) {
        parent::__construct($dsn, $username, $password);
    }
    
    /**
     * @param string $query
     * @param array $parameters
     * @return \PDOStatement
     */
    public function prepareStatement($query, $parameters = []) {
        $statement = $this->prepare($query);
        $statement->execute($parameters);
        return $statement;
    }
    
    /**
     * @param string $tablename
     * @return array
     * @throws \Exception
     */
    public function getColumns($tablename) {
        if (($result = $this->query("SHOW COLUMNS FROM $tablename")) === false) {
            throw new \Exception("Database query failure : {$this->error}");
        }
        $columns = [];
        while ($res = $result->fetch()) {
            $columns[] = strtolower($res[0]);
        }
        return $columns;
    }
    
    /**
     * @param string $colname
     * @param string $table
     * @return bool
     */
    public function columnExists($colname, $table) {
        return (bool) $this->query("SHOW COLUMNS FROM $table LIKE '$colname'")->fetch();
    }
}