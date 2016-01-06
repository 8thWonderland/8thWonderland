<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Message;
use Wonderland\Application\Model\Member;

class MessageRepository extends AbstractRepository
{
    /**
     * @param \Wonderland\Application\Model\Message $message
     *
     * @return \mysqli_result
     */
    public function create(Message $message)
    {
        return $this->connection->prepareStatement(
            'INSERT INTO messages (title, content, author_id, recipient_id, created_at, deleted_by_author, deleted_by_recipient) '.
            'VALUES (:title, :content, :author_id, :recipient_id, :created_at, 0, 0)', [
            'title' => $message->getTitle(),
            'content' => $message->getContent(),
            'author_id' => $message->getAuthor()->getId(),
            'recipient_id' => $message->getRecipient()->getId(),
            'created_at' => $message->getCreatedAt()->format('c'),
        ]);
    }

    /**
     * @param int $id
     *
     * @return \PDOStatement
     */
    public function find($id)
    {
        return $this->connection->prepareStatement(
            'SELECT * FROM messages WHERE id = :id', ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param \Wonderland\Application\Model\Member $recipient
     *
     * @return \PDOStatement
     */
    public function findByRecipient(Member $recipient, $minRange, $maxRange)
    {
        return $this->connection->prepareStatement(
            'SELECT * FROM messages WHERE recipient_id = :recipient_id AND deleted_by_recipient = 0 ' .
            $this->getRangeStatements($minRange, $maxRange)
        , ['recipient_id' => $recipient->getId()]);
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     *
     * @return \PDOStatement
     */
    public function findByAuthor(Member $author)
    {
        return $this->connection->prepareStatement(
            'SELECT * FROM messages WHERE author_id = :author_id AND deleted_by_author = 0', ['author_id' => $author->getId()]);
    }

    /**
     * @param int $id
     * @param int $box
     *
     * @return \PDOStatement
     */
    public function delete($id, $box)
    {
        $deleteField =
            ($box === '1')
            ? 'deleted_by_author'
            : 'deleted_by_recipient'
        ;

        return $this->connection->prepareStatement(
            "UPDATE messages SET $deleteField = 1 WHERE id = :id", ['id' => $id]);
    }

    public function countUnreadMessages($recipientId)
    {
        return (int) $this->connection->prepareStatement(
            'SELECT COUNT(*) AS nb_unread_messages FROM messages WHERE recipient_id = :recipient_id AND opened_at IS NULL AND deleted_by_recipient = 0', ['recipient_id' => $recipientId])->fetch(\PDO::FETCH_ASSOC)['nb_unread_messages'];
    }
    
    public function countReceivedMessages($recipientId) {
        return (int) $this->connection->prepareStatement(
            'SELECT COUNT(*) AS nb_messages FROM messages WHERE recipient_id = :recipient_id AND deleted_by_recipient = 0',
        ['recipient_id' => $recipientId])->fetch(\PDO::FETCH_ASSOC)['nb_messages'];
    }
}
