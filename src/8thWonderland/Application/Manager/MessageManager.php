<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Translator;

use Wonderland\Application\Repository\MessageRepository;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Message;

class MessageManager
{
    /** @var \Wonderland\Application\Manager\MemberManager **/
    protected $memberManager;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    /** @var \Wonderland\Application\Repository\MessageRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Application\Manager\MemberManager        $memberManager
     * @param \Wonderland\Application\Repository\MessageRepository $repository
     */
    public function __construct(MemberManager $memberManager, Translator $translator, MessageRepository $repository)
    {
        $this->memberManager = $memberManager;
        $this->translator = $translator;
        $this->repository = $repository;
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     * @param string                               $recipientIdentity
     * @param string                               $title
     * @param string                               $content
     */
    public function createMessage(Member $author, $recipientIdentity, $title, $content)
    {
        $errors = [];
        if(($recipient = $this->memberManager->getMemberByIdentity($recipientIdentity)) === null) {
            $errors[] = [
                'message' => $this->translator->translate('messages.creation.recipient_not_found')
            ];
        }
        if(trim($title) === '') {
            $errors[] = [
                'message' => $this->translator->translate('messages.creation.empty_content')
            ];
        }
        if(trim($content) === '') {
            $errors[] = [
                'message' => $this->translator->translate('messages.creation.empty_content')
            ];
        }
        if(count($errors) > 0) {
            return $errors;
        }
        
        $message =
            (new Message())
            ->setTitle($title)
            ->setContent($content)
            ->setAuthor($author)
            ->setRecipient($recipient)
            ->setCreatedAt(new \DateTime())
        ;
        $this->repository->create($message);
        
        return true;
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
