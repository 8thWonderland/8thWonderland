<?php

/**
 * class managegroups
 *
 * Gestion des informations des groupes
 *
 * @author Brennan
 */


class managetasks {
    
    // Affichage des taches en cours
    // =============================
    public static function display_tasksinprogress($id_group)
    {
        $db = memory_registry::get('db');
        
        $req = "SELECT idtask, description, date, identite " .
               "FROM tasks, Utilisateurs " .
               "WHERE status = 0 AND id_group=" . $id_group . " AND author=iduser " .
               "ORDER BY date DESC";
        
        return $db->_query($req);
    }
    
    
    // Récupération des détails d'une tâche
    // ====================================
    public function display_detailstask($id)
    {
        $db = memory_registry::get('db');
        $req = "SELECT idtask, description, date, identite FROM tasks, Utilisateurs WHERE author=iduser AND idtask = " . $id;
        return $db->select($req);
    }
    
    
    public function valid_task($description, $date, $id_group)
    {
        if (!isset($description) || empty($description))    {   return -1;      }
        if (!isset($date) || empty($date))                  {   $date = "0000-00-00 00:00:00";     }
        $description = htmlentities($description);
        $auth = auth::getInstance();
        $author = $auth->_getIdentity();
        $db = memory_registry::get('db');
        $req = "INSERT INTO tasks (Description, date, id_group, status, author) " .
               "VALUES ('" . $description . "', '" . $date . "', " . $id_group . ", 0, " . $author . ")";
        $db->_query($req);
        return $db->affected_rows;
    }
    
    
    public function delete_task($id)
    {
        $db = memory_registry::get('db');
        $req = "DELETE FROM tasks WHERE IDTask=" . $id;
        $db->_query($req);
        return $db->affected_rows;
    }
    
}
?>
