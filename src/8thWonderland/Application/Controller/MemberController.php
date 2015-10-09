<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class MemberController extends ActionController {
    public function displayProfileAction() {
        $translate = $this->application->get('translator');
        $this->viewParameters['translate'] = $translate;

        $member = $this->application->get('member_manager')->getMember($this->application->get('session')->get('__id__'));
        // Affichage du genre
        $this->viewParameters['gender'] = 
            ($member->getGender() === 0)
            ? '<option value=1 selected="selected">' . $translate->translate('female') . '</option><option value=2>' . $translate->translate('male') . '</option>'
            : '<option value=1>' . $translate->translate('female') . '</option><option value=2 selected="selected">' . $translate->translate('male') . '</option>'
        ;

        // Affichage des langues
        $langs = $translate->getList();
        $lang_user = $member->getLanguage();
        $sel_lang = ''; $selected = "";
        
        $nbLangs = count($langs);
        
        for ($i = 0; $i < $nbLangs; ++$i) {
            $selected =
                ($langs[$i] === $lang_user)
                ? ' selected="selected"'
                : ''
            ;
            $sel_lang .= "<option value='{$langs[$i]}'{$selected}>{$translate->translate($langs[$i])}</option>";
        }

        // Affichage des pays
        /*$list_country = $member->listCountries();
        $country_user = $member->pays;
        $this->_view['select_country'] = "<option></option>";
        $i=0;
        for ($i=0; $i<count($list_country); $i++) {
            if ($list_country[$i]['Code'] == $country_user)     {   $selected = ' selected="selected"'; }
            else                                                {   $selected = "";                     }
            $this->_view['select_country'] .= "<option value='" . $list_country[$i]['Code'] . "'" . $selected . ">" . $list_country[$i][$lang_user] . "</option>";
        }

        // Affichage de la region du membre
        $region_user = $member->region;
        $db = memory_registry::get("db");
        $region_name = $db->select("SELECT Name FROM regions WHERE Region_id=" . $region_user);*/


        //$this->_view['select_region'] = "<option value='" . $region_user . "' selected='selected'>" . utf8_encode($region_name[0]['Name']) . "</option>";
        $this->viewParameters['langs'] = $sel_lang;
        $this->viewParameters['login'] = $member->getLogin();
        $this->viewParameters['identity'] = $member->getIdentity();
        $this->viewParameters['mail'] = $member->getEmail();
        $this->viewParameters['avatar'] = $member->getAvatar();
        if ($member->getIdentity() === 'Brennan Waco') {
            $this->viewParameters['admin'] = true;
        }
        $this->render('members/update_profile');
    }
    
    public function validProfileAction() {
        $translate = $this->application->get('translator');
        $memberManager = $this->application->get('member_manager');
        $member = $memberManager->getMember($this->application->get('session')->get('__id__'));
        $err_msg = '';
        
        if (!empty($_POST['avatar'])) {
            $res = $member->setAvatar($_POST['avatar']);
            if ($res === 0) {
                $err_msg .= "{$translate->translate('error')}<br/>";
            } elseif ($res === -1) {
                $err_msg .= "{$translate->translate('avatar_invalid')}<br/>";
            }
        }
        if (!empty($_POST['login']) && $member->setLogin($_POST['login']) === 0) {
            $err_msg = "{$translate->translate('error')}<br/>";
        }
        if (!empty($_POST['identity'])) {
            $res = $member->setIdentity($_POST['identity']);
            if ($res === 0) {
                $err_msg .= "{$translate->translate('error')}<br/>";
            } elseif ($res === -1) {
                $err_msg .= "{$translate->translate('identity_invalid')}<br/>";
            } elseif ($res === -2) {
                $err_msg .= "{$translate->translate('identity_exist')}<br/>";
            }
        }
        if (!empty($_POST['password']) && $member->setPassword(hash('sha512', $_POST['password'])) === 0) {
            $err_msg .= "{$translate->translate('error')}<br/>";
        }
        if (!empty($_POST['gender']) && $member->setGender($_POST['gender']) === 0) {
            $err_msg .= "{$translate->translate('error')}<br/>";
        }
        if (!empty($_POST['mail'])) {
            $res = $member->setEmail($_POST['mail']);
            if ($res === 0) {
                $err_msg .= "{$translate->translate('error')}<br/>";
            } elseif ($res === -1) {
                $err_msg .= "{$translate->translate('mail_invalid')}<br/>";
            } elseif ($res === -2) {
                $err_msg .= "{$translate->translate('mail_exist')}<br/>";
            }
        }
        if (!empty($_POST['lang']) && $member->setLanguage($_POST['lang']) === 0) {
            $err_msg .= "{$translate->translate("error")}<br/>";
        }
        $memberManager->updateMember($member);
        if (empty($err_msg)) {
            $this->redirect('Intranet/index');
        } else {
            $this->display(json_encode([
                'status' => 0,
                'reponse' =>
                    '<div class="error" style="height:50px;"><table><tr>' .
                    '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $err_msg . '</span></td>' .
                    '</tr></table></div>'
            ]));
        }
    }
    
    public function searchMembersAction() {
        $this->display('<span>toto, test, tata</span>');
    }
    
    public function displayContactsGroupsAction() {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('member_manager')->getContactGroups());
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translator');
        $tabmini_contactsgroups = 
            '<table class="pagination"><tr class="entete">' .
            '<td width="150px">' . $translate->translate('group_name') . '</td>' .
            '<td width="150px">' . $translate->translate('identity') . '</td>' .
            '</tr>'
        ;

        foreach($datas as $row) {
            $tabmini_contactsgroups .=
                "<tr style='height:25px'><td>{$row['name']}</td>" .
                "<td><a onclick=\"Clic('/Messaging/composeMessage', 'recipient_message={$row['id']}', 'milieu_milieu')\">{$row['identity']}</a></td>" .
                '</tr>'
            ;
        }
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage()) + 1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        
        if ($nLastItem > $paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tabmini_contactsgroups .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/Member/displayContactsGroups\', \'&page=' . ($CurPage - 1) . '\', \'milieu_gauche\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tabmini_contactsgroups .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/member/displayContactsGroups\', \'&page=' . ($CurPage + 1) . '\', \'milieu_gauche\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        $tabmini_contactsgroups .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_contactsgroups'] = $tabmini_contactsgroups;
        $this->viewParameters['translate'] = $translate;
        $this->render('groups/list_contactsgroups');
    }
}