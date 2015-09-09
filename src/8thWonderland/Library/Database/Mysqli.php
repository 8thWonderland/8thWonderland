<?php

namespace Wonderland\Library\Database;

use Wonderland\Library\Application;

class Mysqli extends \mysqli {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param Application $application
     * @throws \Exception
     */
    public function __construct(Application $application) {
        $this->application = $application;
        
        $container = $application->getContainer();
        parent::__construct(
            $container['database_host'],
            $container['database_username'],
            $container['database_password'],
            $container['database_name'])
        ;
        if (mysqli_connect_error()) {
            throw new \Exception('Database connect failure : ' . mysqli_connect_error());
        }
    }
    // Récupère le résultat de la requête et le transforme en array
    // ============================================================
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
    public function count($tablename, $whereClause = '')
    {
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
    public function getColumns($tablename)
    {
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
    public function columnExists($colname, $table)
    {
        return $this->query("SHOW COLUMNS FROM $table LIKE '$colname'")->num_rows;
    }
}