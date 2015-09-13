<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Application;

class MessageManager {
    /**- @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param array $data
     * @return int
     */
    public function createMessage($data) {
        $db = $this->application->get('mysqli');
        
        $title = htmlentities($data['title_message']);
        $msg   = htmlentities($data['content_message']);
        
        $author = $this->application->get('auth')->getIdentity();
        
        $db->query(
            'INSERT INTO messages_received (title, content, author, recipient) ' .
            "VALUES ('$title', '$msg', $author, {$data['recipient_message']});" .
            'INSERT INTO messages_sent (title, content, author, recipients) ' .
            "VALUES ('$title', '$msg', $author, {$data['recipient_message']})"
        );
        
        return $db->affected_rows;
    }
    
    /**
     * @return array
     */
    public function getReceivedMessages() {
        return $this->application->get('mysqli')->select(
            'SELECT id_receivedmessage, title, identite, date_msg ' .
            'FROM messages_received, Utilisateurs ' .
            "WHERE author=Utilisateurs.IDUser AND recipient = {$this->application->get('auth')->getIdentity()} " .
            'ORDER BY date_msg DESC'
        );
    }
    
    /**
     * @return array
     */
    public function getSentMessages() {
        return $this->application->get('mysqli')->select(
            'SELECT id_sentmessage, title, recipients, date_msg ' .
            'FROM messages_sent ' .
            "WHERE author = {$this->application->get('auth')->getIdentity()} " .
            'ORDER BY date_msg DESC'
        );
    }
    
    /**
     * @param int $id
     * @param string $type
     * @return int
     */
    public function deleteMessage($id, $type) {
        $db = $this->application->get('mysqli');
        $db->query(
            ($type === 1)
            ? "DELETE FROM messages_sent WHERE id_sentmessage = $id"
            : "DELETE FROM messages_received WHERE id_receivedmessage = $id"
        );
        return $db->affected_rows;
    }
}