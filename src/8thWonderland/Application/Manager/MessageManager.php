<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Application;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Message;

class MessageManager {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param string $title
     * @param string $content
     * @param \Wonderland\Application\Model\Member $author
     * @param string $recipientIdentity
     * @return \Wonderland\Application\Model\Message
     */
    public function createMessage($title, $content, Member $author, $recipientIdentity) {
        $recipient = $this
            ->application
            ->get('member_manager')
            ->getMemberByIdentity($recipientIdentity)
        ;
        $message =
            (new Message())
            ->setTitle($title)
            ->setContent($content)
            ->setAuthor($author)
            ->setRecipient($recipient)
            ->setCreatedAt(new \DateTime())
        ;
        $this->application->get('message_repository')->create($message);
        return $message;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $recipient
     * @return array
     */
    public function getReceivedMessages(Member $recipient) {
        $statement = $this->application->get('message_repository')->findByRecipient($recipient);
        
        $messages = [];
        
        while($data = $statement->fetch_assoc()) {
            $author = $this->application->get('member_manager')->getMember($data['author_id']);
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
     * @param \Wonderland\Application\Model\Member $author
     * @return array
     */
    public function getSentMessages(Member $author) {
        $statement = $this->application->get('message_repository')->findByAuthor($author);
        
        $messages = [];
        
        while($data = $statement->fetch_assoc()) {
            $recipient = $this->application->get('member_manager')->getMember($data['recipient_id']);
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
     * @return \Wonderland\Application\Model\Message
     */
    public function getMessage($id) {
        $data = $this->application->get('message_repository')->find($id);
        
        $memberManager = $this->application->get('member_manager');
        
        $author = $memberManager->getMember($data['author_id']);
        $recipient = $memberManager->getMember($data['recipient_id']);
        
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
     * @return \mysqli_result
     */
    public function deleteMessage($id, $box) {
        return $this->application->get('message_repository')->delete($id, $box);
    }
}