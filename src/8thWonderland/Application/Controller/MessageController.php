<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;
use Wonderland\Application\Model\Message;
use Wonderland\Library\Admin\Log;

use Wonderland\Library\Http\Response\PaginatedResponse;

class MessageController extends ActionController {
    public function inboxAction() {
        if(($member = $this->getUser()) === null) {
            return $this->redirect('index/index');
        }
        
        $messageManager = $this->application->get('message_manager');
        $memberId = $member->getId();
        
        $range = $this->request->getRange(15);
        $messages = $messageManager->getReceivedMessages($this->getUser(), $range['min'], $range['max']);
        $nbMessages = $messageManager->countReceivedMessages($memberId);
        
        if ($this->is_Ajax()) {
            return new PaginatedResponse([
                    'messages' => $messages,
                    'total_messages' => $nbMessages,
                ],
                $_SERVER['HTTP_RANGE_UNIT'],
                $_SERVER['HTTP_RANGE'],
                $nbMessages
            );
        }
        
        return $this->render('communications/inbox', [
            'messages' => $messages,
            'identity' => $member->getIdentity(),
            'avatar' => $member->getAvatar(),
            'messages_range' => $range['max'],
            'total_messages' => $nbMessages,
            'nb_unread_messages' => $messageManager->countUnreadMessages($memberId),
        ]);
    }
}
