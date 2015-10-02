<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Message;
use Wonderland\Application\Model\Member;

class MessageRepository extends AbstractRepository {
    
    /**
     * @param \Wonderland\Application\Model\Message $message
     * @return \mysqli_result
     */
    public function create(Message $message) {
        return $this->connection->query(
            'INSERT INTO messages (title, content, author, recipient, created_at) ' .
            "VALUES ('{$message->getTitle()}}', '{$message->getContent()}', " .
            "'{$message->getAuthor()->getId()}, {$message->getRecipient()->getId()}), " .
            "'{$message->getCreatedAt()->format('c')}'"
        );
    }
    
    /**
     * @param int $id
     * @return \mysqli_result
     */
    public function find($id) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE id = $id"
        );
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $recipient
     * @return \mysqli_result
     */
    public function findByRecipient(Member $recipient) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE recipient_id = {$recipient->getId()}"
        );
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $author
     * @return \mysqli_result
     */
    public function findByAuthor(Member $author) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE author_id = {$author->getId()}"
        );
    }
    
    /**
     * @param int $id
     * @return \mysqli_result
     */
    public function delete($id) {
        return $this->connection->query(
            "DELETE FROM messages WHERE id = $id"
        );
    }
}