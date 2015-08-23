<?php

namespace Wonderland\Library\Database;

use Wonderland\Library\Memory\Registry;

/**
 * class db_mysqli
 *
 * Utilisation de l'abstraction pour piloter une base de données
 *
 */
class Mysqli extends \mysqli
{
    public function __construct()
    {
        global $application;
        $cfg = $application->getConfig()->getOptions();
        parent::__construct($cfg['db']['host'], $cfg['db']['username'], $cfg['db']['password'], $cfg['db']['dbname']);
        if (mysqli_connect_error()) {
            throw new \Exception('Database connect failure : ' . mysqli_connect_error());
        }
    }
    // Récupère le résultat de la requête et le transforme en array
    // ============================================================
    public function select($query)
    {
        $res = $this->_query($query);
        $result = array(); $num_row = 0;
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $result[$num_row] = $row;
            $num_row++;
        }
        return $result;
    }
    
    // Récupère le nombre de résultats de la requête
    // =============================================
    public function count($tablename, $where='')
    {
        $res = $this->_query("SELECT COUNT(*) FROM " . $tablename . $where);
        $row = $res->fetch_row();
        return $row[0];
    }
    // Récupère le résultat de la requête
    // ==================================
    public function _query($query)
    {
        $result = $this->query($query);
        if ($result === false)      {    throw new \Exception('Database query failure : ' . $this->error);   }
        return $result;
    }
    // Récupère la liste des colonnes d'une table
    // ==========================================
    public function getColumns($tablename)
    {
        $result = $this->_query("SHOW COLUMNS FROM " . $tablename);
        if ($result === false)      {    throw new \Exception('Database query failure : ' . $this->error);   }
        
        $columns = array(); $nCol = 0;
        while ($nCol < $result->num_rows) {
            $res = $result->fetch_row();
            $columns[$nCol] = strtolower($res[0]);
            $nCol++;
        }
        return $columns;
    }
    
    
    // Teste l'existence d'une colonne dans une table
    // ==============================================
    public function ExistColumn($colname, $table)
    {
        $result = $this->_query("SHOW COLUMNS FROM " . $table . " LIKE '" . $colname . "'");
        return $result->num_rows;
    }
}