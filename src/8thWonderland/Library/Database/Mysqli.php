<?php

namespace Wonderland\Library\Database;

class Mysqli extends \mysqli {
    /**
     * @throws \Exception
     */
    public function __construct($host, $username, $password, $databaseName) {
        parent::__construct($host, $username, $password, $databaseName);
        if (mysqli_connect_error()) {
            throw new \Exception('Database connect failure : ' . mysqli_connect_error());
        }
    }
    
    /**
     * @param string
     * @return array
     */
    public function select($query) {
        $res = $this->query($query);
        $result = [];
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }
    
    /**
     * @param string $tablename
     * @param string $whereClause
     * @return int
     */
    public function count($tablename, $whereClause = '') {
        return $this
            ->query("SELECT COUNT(*) FROM $tablename $whereClause")
            ->fetch_row()[0]
        ;
    }
    
    /**
     * @param string $query
     * @return \mysqli_result|false
     * @throws \Exception
     */
    public function query($query) {
        if (($result = parent::query($query)) === false) {
            throw new \Exception('Database query failure : ' . $this->error);
        }
        return $result;
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
        while ($res = $result->fetch_row()) {
            $columns[] = strtolower($res[0]);
        }
        return $columns;
    }
    
    /**
     * @param string $colname
     * @param string $table
     * @return int
     */
    public function columnExists($colname, $table) {
        return $this->query("SHOW COLUMNS FROM $table LIKE '$colname'")->num_rows;
    }
}