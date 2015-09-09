<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Plugin\Paginator;

use Wonderland\Application\Model\Message;

use Wonderland\Library\Database\Mysqli;

use Wonderland\Library\Admin\Log;

class MessagingController extends ActionController {
    
    // Afficher la section de réception de messages
    // ============================================
    public function display_receptionAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;
        $this->render("communications/messaging");
    }
    
    
    // Afficher les messages reçus
    // ===========================
    public function display_receivedmessagesAction()
    {
        $paginator = new Paginator(Message::display_receivedmessages());
        $paginator->_setItemsPage(15);
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = Registry::get('translate');
        $tab_receivedmsg =   '<table class="pagination"><tr class="entete">' .
                             '<td>' . $translate->translate("title_message") . '</td>' .
                             '<td width="140px">' . $translate->translate("sender_message") . '</td>' .
                             '<td width="140px">' . $translate->translate("date_received") . '</td>' .
                             '<td width="50px"></td>' .
                             '</tr>';

        if (count($datas) >0) {
            foreach($datas as $key => $row) {
                $tab_receivedmsg .= "<tr style='height:25px'>";
                foreach($row as $key => $value) {
                    if ($key == "title") {
                        $tab_receivedmsg .= "<td><a onclick=\"Clic('/messaging/display_contentmessage', 'id_msg=" . $row['id_receivedmessage'] . "&box=0', 'volet_visualisation'); return false;\">" . $value . "</a></td>";
                    } elseif ($key != "id_receivedmessage") {
                        $tab_receivedmsg .= "<td>" . $this->_filterReceivedmsg($key, $value) . "</td>";
                    }
                }
                $tab_receivedmsg .= "<td>" .
                            "<a onclick=\"Clic('/messaging/delete_msg', 'id_msg=" . $row['id_receivedmessage'] . "&box=0', 'volet_visualisation'); return false;\">" .
                            "<img height=24 alt='" . $translate->translate('btn_delete') . "' src='" . ICO_PATH . "32x32/delete.png' />" .
                            "</a></td></tr>";
            }
        } else {
            $tab_receivedmsg .= "<tr><td colspan='4'>" . $translate->translate("no_message") . "</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . ($CurPage-1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_receivedmsg .= '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . $page . '\', \'volet_visualisation\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_receivedmsg .= '<b>' . $page . '</b> | ';
            }
        }
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . ($CurPage+1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tab_receivedmsg .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_receivedmessages'] = $tab_receivedmsg;
        $this->viewParameters['translate'] = $translate;
        $this->render('communications/received_messages');
    }
    
    
    // Afficher les messages envoyes
    // =============================
    public function display_sentmessagesAction()
    {
        $paginator = new Paginator(Message::display_sentmessages());
        $paginator->_setItemsPage(15);
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = Registry::get('translate');
        $tab_receivedmsg =   '<table class="pagination"><tr class="entete">' .
                             '<td>' . $translate->translate("title_message") . '</td>' .
                             '<td width="140px">' . $translate->translate("recipient_message") . '</td>' .
                             '<td width="140px">' . $translate->translate("date_received") . '</td>' .
                             '<td width="50px"></td>' .
                             '</tr>';

        if (count($datas) >0) {
            foreach($datas as $key => $row) {
                $tab_receivedmsg .= "<tr style='height:25px'>";
                foreach($row as $key => $value) {
                    if ($key == "title") {
                        $tab_receivedmsg .= "<td><a onclick=\"Clic('/messaging/display_contentmessage', 'id_msg=" . $row['id_sentmessage'] . "&box=1', 'volet_visualisation'); return false;\">" . $value . "</a></td>";
                    } elseif ($key != "id_sentmessage") {
                        $tab_receivedmsg .= "<td>" . $this->_filterSentmsg($key, $value) . "</td>";
                    }
                }
                $tab_receivedmsg .= "<td>" .
                            "<a onclick=\"Clic('/messaging/delete_msg', 'id_msg=" . $row['id_sentmessage'] . "&box=1', 'volet_visualisation'); return false;\">" .
                            "<img height=24 alt='" . $translate->translate('btn_delete') . "' src='" . ICO_PATH . "32x32/delete.png' />" .
                            "</a></td></tr>";
            }
        } else {
            $tab_receivedmsg .= "<tr><td colspan='4'>" . $translate->translate("no_message") . "</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_receivedmsg .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . ($CurPage-1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_receivedmsg .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_receivedmsg .= '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . $page . '\', \'volet_visualisation\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_receivedmsg .= '<b>' . $page . '</b> | ';
            }
        }
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/messaging/display_receivedmessages\', \'&page=' . ($CurPage+1) . '\', \'volet_visualisation\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tab_receivedmsg .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_receivedmessages'] = $tab_receivedmsg;
        $this->viewParameters['translate'] = $translate;
        $this->render('communications/received_messages');
    }
    
    
    // Afficher le contenu d'un message
    // ================================
    public function display_contentmessageAction()
    {
        $db = Mysqli::getInstance();
        $msg = Message::display_contentmessage($_POST['id_msg'], $_POST['box']);
        $this->viewParameters['recipients_message'] = "";
        
        if ($_POST['box'] == 0)     {   $dest[0] = $msg[0]['recipient'];                   }
        else                        {   $dest = explode(',', $msg[0]['recipients']);    }
        for ($i=0; $i<count($dest); $i++) {
            $req = "SELECT Identite FROM Utilisateurs WHERE IDUser = " . $dest[$i] . " LIMIT 1";
            $res = $db->select($req);
            $this->viewParameters['recipients_message'] .= $res[0]['Identite'] . ", ";
        }
        
        if ($_POST['box'] == 1) {   $this->viewParameters['back'] = "display_sentmessages";      }
        else                    {   $this->viewParameters['back'] = "display_receivedmessages";  }
        $this->viewParameters['title_message'] = $msg[0]['title'];
        $this->viewParameters['content_message'] = html_entity_decode($msg[0]['content']);
        $this->viewParameters['date_msg'] = substr($msg[0]['date_msg'], 0, strlen($msg[0]['date_msg'])-3);
        
        $this->viewParameters['translate'] = memory_registry::get('translate');
        $this->render("communications/content_message");
    }
    
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterReceivedmsg($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "identite":
                $value = utf8_encode($value);
                break;
            
            case "date_msg":
                $value = substr($value, 0, strlen($value)-3);
                break;
        }
        
        return $value;
    }
        
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterSentmsg($key, $value)
    {
        $key = strtolower($key);
        switch($key) {           
            case "recipients":
                $db = Registry::get("db");
                $req = "SELECT Identite FROM Utilisateurs WHERE IDUser=" . $value;
                $identity = $db->select($req);
                $value = utf8_encode($identity[0]['Identite']);
                break;
            
            case "date_msg":
                $value = substr($value, 0, strlen($value)-3);
                break;
        }
        
        return $value;
    }
    
    
    // Composer un nouveau message (destinataire inconnu)
    // ==================================================
    public function r_compose_messageAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;
        $this->render("communications/r_compose_message");
    }
        
    
    // Composer un nouveau message (destinataire connu)
    // ================================================
    public function compose_messageAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['recipient_message'] = $_POST['recipient_message'];
        $this->render("communications/compose_message");
    }
    
    
    // Valider un message
    // ==================
    public function valid_messageAction()
    {
        $translate = Registry::get("translate");
        $err_msg = '';
        if (!isset($_POST['title_message']) || empty($_POST['title_message']) || !isset($_POST['content_message']) || empty($_POST['content_message']))
        {
            $err_msg = $translate->translate("fields_empty");
        } else {
            if (Message::create_message($_POST) == 1) {
                
            } else {
                $err_msg = $translate->translate("error");
                
                // Journal de log
                $member = Member::getInstance();
                $db_log = new Log("db");
                $db_log->log("Echec de l'envoi d'un message par l'utilisateur " . $member->identite, Log::ERR);
            }
        }
        
        if (empty($err_msg)) {
            $this->display('<div class="info" style="height:50px;"><table><tr>' .
                          '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->translate("mail_ok") . '</span></td>' .
                          '</tr></table></div>');
        } else {
            $this->display('<div class="error" style="height:50px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $err_msg . '</span></td>' .
                          '</tr></table></div>');
        }
    }
    
    
    // Suppression d'un message
    // ========================
    public function delete_msgAction()
    {
        $translate = Registry::get("translate");
        if (Message::delete_message($_POST['id_msg'], $_POST['box']) > 0) {
            $this->display('<div class="info" style="height:50px;"><table><tr>' .
                           '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->translate('delete_msg_ok') . '</span></td>' .
                           '</tr></table></div>');
        }
        else
        {
            $this->display('<div class="error" style="height:50px;"><table><tr>' .
                           '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->translate('delete_msg_nok') . '</span></td>' .
                           '</tr></table></div>');

            // Journal de log
            $member = Member::getInstance();
            $db_log = new Log("db");
            $db_log->log("Echec de la suppression du message " . $_POST['id_msg'] . " (box=" . $_POST['box'] . ") par l'utilisateur " . $member->identite, Log::ERR);
        }
    }
    
    
    // Créer un nouveau groupe de contact
    // ==================================
    public function create_groupAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;
        $this->render("admin/dev_inprogress");
    }
}