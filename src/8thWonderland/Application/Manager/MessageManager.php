<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Database\PdoDriver;
use Wonderland\Application\Repository\MessageRepository;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Message;

class MessageManager
{
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Application\Manager\MemberManager **/
    protected $memberManager;
    /** @var \Wonderland\Application\Repository\MessageRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Library\Database\PdoDriver               $connection
     * @param \Wonderland\Application\Manager\MemberManager        $memberManager
     * @param \Wonderland\Application\Repository\MessageRepository $repository
     */
    public function __construct(PdoDriver $connection, MemberManager $memberManager, MessageRepository $repository)
    {
        $this->connection = $connection;
        $this->memberManager = $memberManager;
        $this->repository = $repository;
    }

    /**
     * @param string                               $title
     * @param string                               $content
     * @param \Wonderland\Application\Model\Member $author
     * @param string                               $recipientIdentity
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function createMessage($title, $content, Member $author, $recipientIdentity)
    {
        $recipient = $this->memberManager->getMemberByIdentity($recipientIdentity);
        $message =
            (new Message())
            ->setTitle($title)
            ->setContent($content)
            ->setAuthor($author)
            ->setRecipient($recipient)
            ->setCreatedAt(new \DateTime())
        ;
        $this->repository->create($message);

        return $message;
    }

    /**
     * @param \Wonderland\Application\Model\Member $recipient
     *
     * @return array
     */
    public function getReceivedMessages(Member $recipient, $minRange, $maxRange)
    {
        $statement = $this->repository->findByRecipient($recipient, $minRange, $maxRange);

        $messages = [];

        while ($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $author = $this->memberManager->getMember($data['author_id']);
            $messages[] =
                (new Message())
                ->setId($data['id'])
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ->setAuthor($author)
                ->setRecipient($recipient)
                ->setCreatedAt(new \DateTime($data['created_at']))
                ->setOpenedAt(($data['opened_at'] !== null) ? new \DateTime($data['opened_at']) : null)
            ;
        }

        return $messages;
    }
    
    /**
     * @param int $recipientId
     * @return int
     */
    public function countReceivedMessages($recipientId) {
        return $this->repository->countReceivedMessages($recipientId);
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     *
     * @return array
     */
    public function getSentMessages(Member $author)
    {
        $statement = $this->repository->findByAuthor($author);

        $messages = [];

        while ($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $recipient = $this->memberManager->getMember($data['recipient_id']);
            $messages[] =
                (new Message())
                ->setId($data['id'])
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ->setAuthor($author)
                ->setRecipient($recipient)
                ->setCreatedAt(new \DateTime($data['created_at']))
            ;
        }

        return $messages;
    }

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function getMessage($id)
    {
        $data = $this->repository->find($id);

        $author = $this->memberManager->getMember($data['author_id']);
        $recipient = $this->memberManager->getMember($data['recipient_id']);

        return
            (new Message())
            ->setId($id)
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setAuthor($author)
            ->setRecipient($recipient)
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }

    /**
     * @param int $id
     * @param int $box
     *
     * @return int
     */
    public function deleteMessage($id, $box)
    {
        return $this->repository->delete($id, $box)->rowCount();
    }

    /**
     * @param int $recipientId
     *
     * @return int
     */
    public function countUnreadMessages($recipientId)
    {
        return $this->repository->countUnreadMessages($recipientId);
    }
}
