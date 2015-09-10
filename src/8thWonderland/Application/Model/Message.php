<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Auth;

/**
 * class managegroups
 *
 * Gestion des informations des groupes
 *
 * @author Brennan
 */
class Message {
    
    // Enregistre un nouveau message
    // =============================
    public static function create_message($datas)
    {
        $db = Registry::get('db');
        
        $title = htmlentities($datas['title_message']);
        $msg   = htmlentities($datas['content_message']);
        $auth = Auth::getInstance();
        $author = $auth->getIdentity();
        
        $req = "INSERT INTO messages_received (title, content, author, recipient) VALUES ('" . $title . "', '" . $msg . "', " . $author . ", " . $datas['recipient_message'] . ")";
        $db->query($req);
        $req = "INSERT INTO messages_sent (title, content, author, recipients) VALUES ('" . $title . "', '" . $msg . "', " . $author . ", " . $datas['recipient_message'] . ")";
        $db->query($req);
        
        return $db->affected_rows;
    }
    
    
    // Affichage des messages reçus
    // ============================
    public static function display_receivedmessages()
    {
        return Registry::get('db')->select(
            'SELECT id_receivedmessage, title, identite, date_msg ' .
            'FROM messages_received, Utilisateurs ' .
            'WHERE author=Utilisateurs.IDUser AND recipient=' . Auth::getInstance()->getIdentity() . ' ' .
            'ORDER BY date_msg DESC'
        );
    }
        
    
    // Affichage des messages envoyés
    // ==============================
    public static function display_sentmessages() {
        return Registry::get('db')->select(
            'SELECT id_sentmessage, title, recipients, date_msg ' .
            'FROM messages_sent ' .
            'WHERE author=' . Auth::getInstance()->getIdentity() . ' ' .
            'ORDER BY date_msg DESC'
        );
    }
    
    /**
     * @param int $id
     * @param string $type
     * @return array
     */
    public static function display_contentmessage($id, $type) {
        $db = Registry::get('db');
        $auth = Auth::getInstance();
        
        $req =
            ($type === 1)
            ? 
                "SELECT title, content, recipients, date_msg " .
                "FROM messages_sent " .
                "WHERE author=" . $auth->getIdentity() . " " .
                "ORDER BY date_msg DESC"
            :
                "SELECT title, content, recipient, date_msg " .
                "FROM messages_received, Utilisateurs " .
                "WHERE author=Utilisateurs.IDUser AND recipient=" . $auth->getIdentity() . " " .
                "ORDER BY date_msg DESC"
        ;
        return $db->select($req);
    }
    
    /**
     * @param int $id
     * @param string $type
     * @return int
     */
    public static function delete_message($id, $type) {
        $db = Registry::get('db');
        $db->query(
            ($type === 1)
            ? "DELETE FROM messages_sent WHERE id_sentmessage=$id"
            : "DELETE FROM messages_received WHERE id_receivedmessage=$id"
        );
        return $db->affected_rows;
    }
}