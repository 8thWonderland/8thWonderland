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
        $nbMessages = $messageManager->countReceivedMessages($memberId);
        
        if ($this->is_Ajax()) {
            return new PaginatedResponse([
                    'messages' => $messageManager->getReceivedMessages($this->getUser(), $range['min'], $range['max']),
                    'total_messages' => $nbMessages,
                ],
                $_SERVER['HTTP_RANGE_UNIT'],
                $_SERVER['HTTP_RANGE'],
                $nbMessages
            );
        }
        
        return $this->render('communications/inbox', [
            'messages' => $messageManager->getReceivedMessages($this->getUser()),
            'identity' => $member->getIdentity(),
            'avatar' => $member->getAvatar(),
            'messages_range' => $range['max'],
            'total_messages' => $nbMessages,
            'nb_unread_messages' => $messageManager->countUnreadMessages($memberId),
        ]);
    }

    public function displayReceivedMessagesAction()
    {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this
            ->application->get('message_manager')
            ->getReceivedMessages($this->getUser())
        );
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $data = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();

        $translate = $this->application->get('translator');
        $tab_receivedmsg =
            '<table class="pagination"><tr class="entete">'.
            '<td>'.$translate->translate('title_message').'</td>'.
            '<td width="140px">'.$translate->translate('sender_message').'</td>'.
            '<td width="140px">'.$translate->translate('date_received').'</td>'.
            '<td width="50px"></td></tr>'
        ;

        $nbData = count($data);
        for ($i = 0; $i < $nbData; ++$i) {
            $tab_receivedmsg .=
                "<tr style='height:25px'>".
                "<td><a onclick=\"Clic('Messaging/displayContentMessage', 'id_msg={$data[$i]->getId()}&box=0', 'volet_visualisation'); return false;\">{$data[$i]->getTitle()}</a></td> ".
                "<td>{$data[$i]->getAuthor()->getIdentity()}</td>".
                "<td>{$data[$i]->getCreatedAt()->format('d/m/Y H:i:s')}</td>".
                "<td><a onclick=\"Clic('Messaging/deleteMessage', 'id_msg={$data[$i]->getId()}&box=0', 'volet_visualisation'); return false;\">".
                "<img height=24 alt='{$translate->translate('btn_delete')}' src='".ICO_PATH."32x32/delete.png' />".
                '</a></td></tr>'
            ;
        }
        if ($nbData === 0) {
            $tab_receivedmsg .= "<tr><td colspan='4'>{$translate->translate('no_message')}</td></tr>";
        }

        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage()) + 1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());

        if ($nLastItem > $paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">'.$nFirstItem.'-'.$nLastItem.$translate->translate('item_of').$paginator->countItems().'</td>';

        // boutons precedent
        $previous = '<span class="disabled">'.$translate->translate('page_previous').'</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.($CurPage - 1).'\', \'volet_visualisation\'); return false;">'.$translate->translate('page_previous').'</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">'.$previous.' | ';

        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();

        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }

        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_receivedmsg .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.$page.'\', \'volet_visualisation\'); return false;">'.$page.'</a> | '
                : "<b>$page</b> | "
            ;
        }
        // Bouton suivant
        $next = '<span class="disabled">'.$translate->translate('page_next').'</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.($CurPage + 1).'\', \'volet_visualisation\'); return false;">'.$translate->translate('page_next').'</a>';
        }

        $tab_receivedmsg .= $next.'</td></tr></table>';

        return $this->render('communications/received_messages', [
            'list_receivedmessages' => $tab_receivedmsg,
        ]);
    }

    public function displaySentMessagesAction()
    {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this
            ->application
            ->get('message_manager')
            ->getSentMessages($this->getUser())
        );
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $data = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();

        $translate = $this->application->get('translator');
        $tab_receivedmsg =
            '<table class="pagination"><tr class="entete">'.
            '<td>'.$translate->translate('title_message').'</td>'.
            '<td width="140px">'.$translate->translate('recipient_message').'</td>'.
            '<td width="140px">'.$translate->translate('date_received').'</td>'.
            '<td width="50px"></td></tr>'
        ;

        $nbData = count($data);
        for ($i = 0; $i < $nbData; ++$i) {
            $tab_receivedmsg .=
                "<tr style='height:25px'>".
                "<td><a onclick=\"Clic('Messaging/displayContentMessage', 'id_msg={$data[$i]->getId()}&box=1', 'volet_visualisation'); return false;\">{$data[$i]->getTitle()}</a></td>".
                "<td>{$data[$i]->getRecipient()->getIdentity()}</td>".
                "<td>{$data[$i]->getCreatedAt()->format('d/m/y H:i:s')}</td>".
                "<td><a onclick=\"Clic('Messaging/deleteMessage', 'id_msg={$data[$i]->getId()}&box=1', 'volet_visualisation'); return false;\">".
                "<img height=24 alt='{$translate->translate('btn_delete')}' src='".ICO_PATH."32x32/delete.png' />".
                '</a></td></tr>'
            ;
        }
        if ($nbData === 0) {
            $tab_receivedmsg .= "<tr><td colspan='4'>{$translate->translate('no_message')}</td></tr>";
        }

        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage()) + 1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());

        if ($nLastItem > $paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">'.$nFirstItem.'-'.$nLastItem.$translate->translate('item_of').$paginator->countItems().'</td>';

        // boutons precedent
        $previous = '<span class="disabled">'.$translate->translate('page_previous').'</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.($CurPage - 1).'\', \'volet_visualisation\'); return false;">'.$translate->translate('page_previous').'</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">'.$previous.' | ';

        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }

        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_receivedmsg .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.$page.'\', \'volet_visualisation\'); return false;">'.$page.'</a> | '
                : "<b>$page</b> | "
            ;
        }
        // Bouton suivant
        $next = '<span class="disabled">'.$translate->translate('page_next').'</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Messaging/displayReceivedMessages\', \'&page='.($CurPage + 1).'\', \'volet_visualisation\'); return false;">'.$translate->translate('page_next').'</a>';
        }

        return $this->render('communications/received_messages', [
            'list_receivedmessages' => $tab_receivedmsg.$next.'</td></tr></table>',
        ]);
    }

    public function displayContentMessageAction()
    {
        $message = $this->application->get('message_manager')->getMessage($_POST['id_msg']);

        return $this->render('communications/content_message', [
            'recipients_message' => $message->getRecipient()->getIdentity(),
            'back' => ($_POST['box'] === 1)
                ? 'displaySentMessages'
                : 'displayReceivedMessages',
            'title_message' => $message->getTitle(),
            'content_message' => $message->getContent(),
            'date_msg' => $message->getCreatedAt()->format('d/m/Y H:i:s'),
        ]);
    }

    public function composeMessageToUnknownAction()
    {
        return $this->render('communications/r_compose_message');
    }

    public function composeMessageAction()
    {
        return $this->render('communications/compose_message', [
            'recipient_message' => $_POST['recipient_message'],
        ]);
    }

    public function validateMessageAction()
    {
        $translate = $this->application->get('translator');
        $author = $this->getUser();

        if (empty($_POST['title_message']) || empty($_POST['content_message'])) {
            $err_msg = $translate->translate('fields_empty');
        } elseif (!$this->application->get('message_manager')->createMessage($_POST['title_message'], $_POST['content_message'], $author, $_POST['recipient_message']) instanceof Message) {
            $err_msg = $translate->translate('error');

            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec de l'envoi d'un message par l'utilisateur {$member->getIdentity()}", Log::ERR);
        }

        if (empty($err_msg)) {
            return new Response(
                '<div class="info" style="height:50px;"><table><tr>'.
                '<td><img alt="info" src="'.ICO_PATH.'64x64/Info.png" style="width:48px;"/></td>'.
                '<td><span style="font-size: 15px;">'.$translate->translate('mail_ok').'</span></td>'.
                '</tr></table></div>'
            );
        }

        return new Response(
            '<div class="error" style="height:50px;"><table><tr>'.
            '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:48px;"/></td>'.
            '<td><span style="font-size: 15px;">'.$err_msg.'</span></td>'.
            '</tr></table></div>'
        );
    }

    public function deleteMessageAction()
    {
        $translator = $this->application->get('translator');
        if ($this->application->get('message_manager')->deleteMessage($_POST['id_msg'], $_POST['box']) > 0) {
            return new Response(
                '<div class="info" style="height:50px;"><table><tr>'.
                '<td><img alt="info" src="'.ICO_PATH.'64x64/Info.png" style="width:48px;"/></td>'.
                '<td><span style="font-size: 15px;">'.$translator->translate('delete_msg_ok').'</span></td>'.
                '</tr></table></div>'
            );
        }
        $member = $this->application->get('member_manager')->getMember($this->application->get('session')->get('__id__'));
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $logger->log("Echec de la suppression du message {$_POST['id_msg']} (box={$_POST['box']}) par l'utilisateur {$member->getIdentity()}", Log::ERR);

        return new Response(
            '<div class="error" style="height:50px;"><table><tr>'.
            '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:48px;"/></td>'.
            '<td><span style="font-size: 15px;">'.$translator->translate('delete_msg_nok').'</span></td>'.
            '</tr></table></div>'
        );
    }

    public function createGroupAction()
    {
        return $this->render('admin/dev_inprogress');
    }
}
