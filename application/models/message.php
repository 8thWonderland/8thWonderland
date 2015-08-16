<?php

/**
 * class managegroups
 *
 * Gestion des informations des groupes
 *
 * @author Brennan
 */


class message {
    
    // Enregistre un nouveau message
    // =============================
    public static function create_message($datas)
    {
        $db = memory_registry::get('db');
        
        $title = htmlentities($datas['title_message']);
        $msg   = htmlentities($datas['content_message']);
        $auth = auth::getInstance();
        $author = $auth->_getIdentity();
        
        $req = "INSERT INTO messages_received (title, content, author, recipient) VALUES ('" . $title . "', '" . $msg . "', " . $author . ", " . $datas['recipient_message'] . ")";
        $db->_query($req);
        $req = "INSERT INTO messages_sent (title, content, author, recipients) VALUES ('" . $title . "', '" . $msg . "', " . $author . ", " . $datas['recipient_message'] . ")";
        $db->_query($req);
        
        return $db->affected_rows;
    }
    
    
    // Affichage des messages reçus
    // ============================
    public static function display_receivedmessages()
    {
        $db = memory_registry::get('db');
        $auth = auth::getInstance();
        
        $req = "SELECT id_receivedmessage, title, identite, date_msg " .
               "FROM messages_received, Utilisateurs " .
               "WHERE author=Utilisateurs.IDUser AND recipient=" . $auth->_getIdentity() . " " .
               "ORDER BY date_msg DESC";

        return $db->select($req);
    }
        
    
    // Affichage des messages envoyés
    // ==============================
    public static function display_sentmessages()
    {
        $db = memory_registry::get('db');
        $auth = auth::getInstance();
        
        $req = "SELECT id_sentmessage, title, recipients, date_msg " .
               "FROM messages_sent " .
               "WHERE author=" . $auth->_getIdentity() . " " .
               "ORDER BY date_msg DESC";

        return $db->select($req);
    }
    
    
    // Affichage du contenu d'un message
    // =================================
    public static function display_contentmessage($id, $type)
    {
        $db = memory_registry::get('db');
        $auth = auth::getInstance();
        
        if ($type == 1) {
            $req = "SELECT title, content, recipients, date_msg " .
                   "FROM messages_sent " .
                   "WHERE author=" . $auth->_getIdentity() . " " .
                   "ORDER BY date_msg DESC";
        } else {
            $req = "SELECT title, content, recipient, date_msg " .
                   "FROM messages_received, Utilisateurs " .
                   "WHERE author=Utilisateurs.IDUser AND recipient=" . $auth->_getIdentity() . " " .
                   "ORDER BY date_msg DESC";
        }

        return $db->select($req);
    }
    
    
    // Suppression d'un message
    // ========================
    public static function delete_message($id, $type)
    {
        $db = memory_registry::get('db');
        
        if ($type == 1) {
            $req = "DELETE FROM messages_sent " .
                   "WHERE id_sentmessage=" . $id;
        } else {
            $req = "DELETE FROM messages_received " .
                   "WHERE id_receivedmessage=" . $id;
        }

        $db->_query($req);
        return $db->affected_rows;
    }
}
?>
