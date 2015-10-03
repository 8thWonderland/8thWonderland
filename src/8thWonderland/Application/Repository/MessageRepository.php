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
            'INSERT INTO messages (title, content, author_id, recipient_id, created_at) ' .
            "VALUES ('{$message->getTitle()}', '{$message->getContent()}', " .
            "{$message->getAuthor()->getId()}, {$message->getRecipient()->getId()}, " .
            "'{$message->getCreatedAt()->format('c')}')"
        );
    }
    
    /**
     * @param int $id
     * @return \mysqli_result
     */
    public function find($id) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE id = $id"
        )->fetch_assoc();
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $recipient
     * @return \mysqli_result
     */
    public function findByRecipient(Member $recipient) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE recipient_id = {$recipient->getId()} AND deleted_by_recipient = 0"
        );
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $author
     * @return \mysqli_result
     */
    public function findByAuthor(Member $author) {
        return $this->connection->query(
            "SELECT * FROM messages WHERE author_id = {$author->getId()} AND deleted_by_author = 0"
        );
    }
    
    /**
     * @param int $id
     * @param int $box
     * @return \mysqli_result
     */
    public function delete($id, $box) {
        $deleteField =
            ($box === '1')
            ? 'deleted_by_author'
            : 'deleted_by_recipient'
        ;
        return $this->connection->query(
            "UPDATE messages SET $deleteField = 1 WHERE id = $id"
        );
    }
}