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
     * @param int $memberId
     * @param int $id
     * 
     * @return \Wonderland\Application\Model\Message|null
     */
    public function find($memberId, $id)
    {
        $data = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.content, m.created_at, m.opened_at, ' .
            'author.id as author_id, author.identity as author_identity, author.avatar as author_avatar, ' .
            'recipient.id as recipient_id, recipient.identity as recipient_identity, recipient.avatar as recipient_avatar ' .
            'FROM messages m ' .
            'INNER JOIN users author ON author.id = m.author_id ' .
            'INNER JOIN users recipient ON recipient.id = m.recipient_id ' .
            'WHERE m.id = :id AND (author.id = :member_id OR recipient.id = :member_id)'
        , ['id' => $id, 'member_id' => $memberId])->fetch(\PDO::FETCH_ASSOC);
        if(!$data) {
            return null;
        }
        if((int)$data['recipient_id'] === $memberId) {
            $affectedRows = $this->connection->exec("UPDATE messages SET opened_at = NOW() WHERE id = $id");
            if($affectedRows === 0) {
                $this->throwPdoException();
            }
        }
        return $this->formatObject($data);
    }

    /**
     * @param \Wonderland\Application\Model\Member $recipient
     *
     * @return array
     */
    public function findByRecipient(Member $recipient, $minRange, $maxRange)
    {
        $statement = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.content, m.created_at, m.opened_at, ' .
            'u.id as author_id, u.avatar as author_avatar, u.identity as author_identity ' .
            'FROM messages m ' .
            'INNER JOIN users u ON u.id = m.author_id ' .
            'WHERE m.recipient_id = :recipient_id AND m.deleted_by_recipient = 0 ' .
            'ORDER BY m.created_at DESC ' .
            $this->getRangeStatements($minRange, $maxRange)
        , ['recipient_id' => $recipient->getId()]);
        
        $result = [];
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $data['recipient_id'] = $recipient->getId();
            $data['recipient_identity'] = $recipient->getIdentity();
            $data['recipient_avatar'] = $recipient->getAvatar();
            $result[] = $this->formatObject($data);
        }
        return $result;
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     *
     * @return array
     */
    public function findByAuthor(Member $author)
    {
        $statement = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.content, m.created_at, m.opened_at, ' .
            'u.id as recipient_id, u.avatar as recipient_avatar, u.identity as recipient_identity, ' .
            'FROM messages m ' .
            'INNER JOIN users u ON u.id = m.recipient_id ' .
            'WHERE m.author_id = :author_id AND m.deleted_by_author = 0', ['author_id' => $author->getId()]);
        
        $result = [];
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $data['author_id'] = $author->getId();
            $data['author_identity'] = $author->getIdentity();
            $data['author_avatar'] = $author->getAvatar();
            $result[] = $this->formatObject($data);
        }
        return $result;
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

    /**
     * @param int $recipientId
     * @return int
     */
    public function countUnreadMessages($recipientId)
    {
        return (int) $this->connection->prepareStatement(
            'SELECT COUNT(*) AS nb_unread_messages FROM messages WHERE recipient_id = :recipient_id AND opened_at IS NULL AND deleted_by_recipient = 0', ['recipient_id' => $recipientId])->fetch(\PDO::FETCH_ASSOC)['nb_unread_messages'];
    }
    
    /**
     * @param int $recipientId
     * @return int
     */
    public function countReceivedMessages($recipientId) {
        return (int) $this->connection->prepareStatement(
            'SELECT COUNT(*) AS nb_messages FROM messages WHERE recipient_id = :recipient_id AND deleted_by_recipient = 0',
        ['recipient_id' => $recipientId])->fetch(\PDO::FETCH_ASSOC)['nb_messages'];
    }
    
    public function formatObject($data) {
        return 
            (new Message())
            ->setId((int)$data['id'])
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setAuthor(
                (new Member())
                ->setId((int)$data['author_id'])
                ->setAvatar($data['author_avatar'])
                ->setIdentity($data['author_identity'])
            )
            ->setRecipient(
                (new Member())
                ->setId((int)$data['recipient_id'])
                ->setIdentity($data['recipient_identity'])
                ->setAvatar($data['recipient_avatar'])
            )
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setOpenedAt(($data['opened_at'] !== null) ? new \DateTime($data['opened_at']) : null)
        ;
    }
}
