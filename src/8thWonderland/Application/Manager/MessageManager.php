<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Repository\MessageRepository;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Message;

class MessageManager
{
    /** @var \Wonderland\Application\Manager\MemberManager **/
    protected $memberManager;
    /** @var \Wonderland\Application\Repository\MessageRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Application\Manager\MemberManager        $memberManager
     * @param \Wonderland\Application\Repository\MessageRepository $repository
     */
    public function __construct(MemberManager $memberManager, MessageRepository $repository)
    {
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
        return $this->repository->findByRecipient($recipient, $minRange, $maxRange);
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
        return $this->repository->findByAuthor($author);
    }

    /**
     * @param int $memberId
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Message|null
     */
    public function getMessage($memberId, $id)
    {
        return $this->repository->find($memberId, $id);
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
