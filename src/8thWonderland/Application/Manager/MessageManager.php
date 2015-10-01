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
        
        $author = $this->application->get('session')->get('__id__');
        
        $db->query(
            'INSERT INTO messages_received (title, content, author, recipient) ' .
            "VALUES ('$title', '$msg', $author, '{$data['recipient_message']}')"
        );
        $db->query(
            'INSERT INTO messages_sent (title, content, author, recipients) ' .
            "VALUES ('$title', '$msg', $author, '{$data['recipient_message']}')"
        );
        
        return $db->affected_rows;
    }
    
    /**
     * @return array
     */
    public function getReceivedMessages() {
        return $this->application->get('mysqli')->select(
            'SELECT id_receivedmessage, title, identity, date_msg ' .
            'FROM messages_received, users ' .
            "WHERE author=users.id AND recipient = {$this->application->get('session')->get('__id__')} " .
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
            "WHERE author = {$this->application->get('session')->get('__id__')} " .
            'ORDER BY date_msg DESC'
        );
    }
    
    /**
     * @param int $id
     * @param int $type
     * @return \mysqli_result
     */
    public function getMessage($id, $type) {
        $db = $this->application->get('mysqli');
        return
            ($type === 1)
            ? $db->select(
                'SELECT title, content, recipients, date_msg ' .
                "FROM messages_sent WHERE id_sentmessage = $id " .
                'ORDER BY date_msg DESC'
            ) : $db->select(
                'SELECT title, content, recipient, date_msg ' .
                "FROM messages_received WHERE id_receivedmessage = $id " .
                'ORDER BY date_msg DESC'
            )
        ;
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