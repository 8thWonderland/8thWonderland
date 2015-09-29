<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Application\Model\Message;

use Wonderland\Library\Admin\Log;

class MessagingController extends ActionController {
    public function displayReceptionAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('communications/messaging');
    }
    
    public function displayReceivedMessagesAction() {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('message_manager')->getReceivedMessages());
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        
        $translate = $this->application->get('translate');
        $tab_receivedmsg = 
            '<table class="pagination"><tr class="entete">' .
            '<td>' . $translate->translate("title_message") . '</td>' .
            '<td width="140px">' . $translate->translate("sender_message") . '</td>' .
            '<td width="140px">' . $translate->translate("date_received") . '</td>' .
            '<td width="50px"></td></tr>'
        ;
        
        $nbData = count($datas);
        for($i = 0; $i < $nbData; ++$i) {
            $tab_receivedmsg .= "<tr style='height:25px'>";
            foreach($datas[$i] as $key => $value) {
                $tab_receivedmsg .=
                    ($key === 'title')
                    ? "<td><a onclick=\"Clic('/Messaging/displayContentMessage', 'id_msg={$datas[$i]['id_receivedmessage']}&box=0', 'volet_visualisation'); return false;\">$value</a></td>"
                    : "<td>{$this->filterReceivedmsg($key, $value)}</td>"
                ;
            }
            $tab_receivedmsg .=
                "<td><a onclick=\"Clic('/Messaging/deleteMessage', 'id_msg={$datas[$i]['id_receivedmessage']}&box=0', 'volet_visualisation'); return false;\">" .
                "<img height=24 alt='{$translate->translate('btn_delete')}' src='" . ICO_PATH . "32x32/delete.png' />" .
                '</a></td></tr>'
            ;
        }
        if ($nbData === 0) {
            $tab_receivedmsg .= "<tr><td colspan='4'>{$translate->translate('no_message')}</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage()) + 1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        
        if ($nLastItem>$paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . ($CurPage-1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        
        if ($start<1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_receivedmsg .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . $page . '\', \'volet_visualisation\'); return false;">' . $page . '</a> | '
                : "<b>$page</b> | "
            ;
        }
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . ($CurPage + 1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tab_receivedmsg .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_receivedmessages'] = $tab_receivedmsg;
        $this->viewParameters['translate'] = $translate;
        $this->render('communications/received_messages');
    }
    
    public function displaySentMessagesAction() {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('message_manager')->getSentMessages());
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        
        $translate = $this->application->get('translate');
        $tab_receivedmsg =  
            '<table class="pagination"><tr class="entete">' .
            '<td>' . $translate->translate('title_message') . '</td>' .
            '<td width="140px">' . $translate->translate("recipient_message") . '</td>' .
            '<td width="140px">' . $translate->translate("date_received") . '</td>' .
            '<td width="50px"></td></tr>'
        ;

        $nbData = count($datas);
        for($i = 0; $i < $nbData; ++$i) {
            $tab_receivedmsg .= "<tr style='height:25px'>";
            foreach($datas[$i] as $key => $value) {
                $tab_receivedmsg .=
                    ($key === 'title')
                    ? "<td><a onclick=\"Clic('/Messaging/displayContentMessage', 'id_msg={$datas[$i]['id_sentmessage']}&box=1', 'volet_visualisation'); return false;\">$value</a></td>"
                    : "<td>{$this->filterSentmsg($key, $value)}</td>"
                ;
            }
            $tab_receivedmsg .=
                "<td><a onclick=\"Clic('/Messaging/deleteMessage', 'id_msg={$datas[$i]['id_sentmessage']}&box=1', 'volet_visualisation'); return false;\">" .
                "<img height=24 alt='{$translate->translate('btn_delete')}' src='" . ICO_PATH . "32x32/delete.png' />" .
                "</a></td></tr>"
            ;
        }
        if ($nbData === 0) {
            $tab_receivedmsg .= "<tr><td colspan='4'>{$translate->translate('no_message')}</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        
        if ($nLastItem>$paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . ($CurPage-1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        
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
                ? '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . $page . '\', \'volet_visualisation\'); return false;">' . $page . '</a> | '
                : "<b>$page</b> | "
            ;
        }
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/Messaging/displayReceivedMessages\', \'&page=' . ($CurPage + 1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $this->viewParameters['list_receivedmessages'] = $tab_receivedmsg . $next . '</td></tr></table>';
        $this->viewParameters['translate'] = $translate;
        $this->render('communications/received_messages');
    }
    
    public function displayContentMessageAction() {
        $db = $this->application->get('mysqli');
        $msg = Message::display_contentmessage($_POST['id_msg'], $_POST['box']);
        $this->viewParameters['recipients_message'] = '';
        
        $dest =
            ($_POST['box'] === 0)
            ? [$msg[0]['recipient']]
            : explode(',', $msg[0]['recipients'])
        ;
        $nbDest = count($dest);
        for ($i = 0; $i < $nbDest; ++$i) {
            $this->viewParameters['recipients_message'] .= $db->select("SELECT Identite FROM Utilisateurs WHERE IDUser = {$dest[$i]} LIMIT 1")[0]['Identite'] . ', ';
        }
        
        $this->viewParameters['back'] =
            ($_POST['box'] === 1)
            ? 'display_sentmessages'
            : 'display_receivedmessages'
        ;
        $this->viewParameters['title_message'] = $msg[0]['title'];
        $this->viewParameters['content_message'] = html_entity_decode($msg[0]['content']);
        $this->viewParameters['date_msg'] = substr($msg[0]['date_msg'], 0, strlen($msg[0]['date_msg'])-3);
        
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('communications/content_message');
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterReceivedmsg($key, $value) {
        switch(strtolower($key)) {
            case 'identite':
                return utf8_encode($value);
            
            case 'date_msg':
                return substr($value, 0, strlen($value)-3);
        }
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterSentmsg($key, $value) {
        switch(strtolower($key)) {           
            case 'recipients':
                return utf8_encode($this
                    ->application
                    ->get('mysqli')
                    ->select("SELECT identity FROM users WHERE id = $value")
                [0]['Identite']);
            
            case 'date_msg':
                return substr($value, 0, strlen($value) - 3);
        }
    }
    
    public function composeMessageToUnknownAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('communications/r_compose_message');
    }
    
    public function composeMessageAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->viewParameters['recipient_message'] = $_POST['recipient_message'];
        $this->render("communications/compose_message");
    }
    
    public function validateMessageAction() {
        $translate = $this->application->get('translate');
        
        if (empty($_POST['title_message']) || empty($_POST['content_message'])) {
            $err_msg = $translate->translate('fields_empty');
        } elseif (Message::create_message($_POST) !== 1) {
            $err_msg = $translate->translate('error');

            $member = Member::getInstance();
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec de l'envoi d'un message par l'utilisateur {$member->identite}", Log::ERR);
        }
        
        if (empty($err_msg)) {
            $this->display(
                '<div class="info" style="height:50px;"><table><tr>' .
                '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate("mail_ok") . '</span></td>' .
                '</tr></table></div>'
            );
        } else {
            $this->display(
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $err_msg . '</span></td>' .
                '</tr></table></div>'
            );
        }
    }
    
    public function deleteMessageAction() {
        $translate = $this->application->get('translate');
        if (Message::delete_message($_POST['id_msg'], $_POST['box']) > 0) {
            $this->display(
                '<div class="info" style="height:50px;"><table><tr>' .
                '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('delete_msg_ok') . '</span></td>' .
                '</tr></table></div>'
            );
        } else {
            $this->display(
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('delete_msg_nok') . '</span></td>' .
                '</tr></table></div>'
            );
            $member = Member::getInstance();
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec de la suppression du message {$_POST['id_msg']} (box={$_POST['box']}) par l'utilisateur {$member->identite}", Log::ERR);
        }
    }
    
    public function createGroupAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/dev_inprogress');
    }
}