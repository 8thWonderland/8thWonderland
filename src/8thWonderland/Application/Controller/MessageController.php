<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Http\Response\JsonResponse;
use Wonderland\Library\Http\Response\PaginatedResponse;

use Wonderland\Library\Exception\NotFoundException;

class MessageController extends ActionController {
    public function inboxAction() {
        if(($member = $this->getUser()) === null) {
            return $this->redirect('public/index');
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
    
    /**
     * @return \Wonderland\Library\Http\Response\JsonResponse
     * @throws NotFoundException
     */
    public function readAction() {
        $message = $this->application->get('message_manager')->getMessage(
            $this->getUser()->getId(),
            $this->request->get('message_id', null, 'int')
        );
        if($message === null) {
            throw new NotFoundException('Message Not Found');
        }
        return new JsonResponse([
            'message' => $message
        ]);
    }
    
    public function createAction() {
        $this->checkAccess('citizenship');
        
        $result = $this->application->get('message_manager')->createMessage(
            $this->getUser(),
            $this->request->get('recipient'),
            $this->request->get('title'),
            $this->request->get('content')
        );
        if($result !== true) {
            return new JsonResponse($result, 400);
        }
        return new JsonResponse([
            'message' => $this->application->get('translator')->translate('messages.creation.success')
        ]);
    }
}
