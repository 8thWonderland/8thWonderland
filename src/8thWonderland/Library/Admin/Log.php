<?php

namespace Wonderland\Library\Admin;

use Wonderland\Library\Memory\Registry;

/**
 * class admin_logs
 *
 * Mise en place d'un système de log
 *
 */
class Log
{
    const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages
    
    protected $_priorities = array();                                           // Tableau des constantes
    protected $_writers = array(0 => "DB", 1=> "MAIL", 2 => "FILE");            // Type de writers possibles
    protected $writer;                                                          // writer choisi pour cette instance
    
    
    public function __construct($writer)
    {
        $r = new \ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());
        if (in_array(strtoupper($writer), $this->_writers))     {   $this->writer = strtoupper($writer);    }
    }
    
    
    // Enregistrement d'un log d'event
    // ===============================
    public function log($message, $priority)
    {       
        switch ($this->writer)
        {
            case "DB":
                $db = Registry::get('db');
                $db->query("INSERT INTO logs (level, msg) VALUES (" . $priority . ", '" . $db->real_escape_string($message) . "')");
                break;

            case "MAIL":
                break;

            case "FILE":
                break;
        }
    }
        
    
    // Affichage des motions archivées
    // ===============================
    public static function display_dblogs()
    {
        $db = Registry::get('db');

        $req = "SELECT level, timelogs, msg " .
               "FROM logs " .
               "ORDER BY timelogs DESC";

        return $db->select($req);
    }
}

?>
