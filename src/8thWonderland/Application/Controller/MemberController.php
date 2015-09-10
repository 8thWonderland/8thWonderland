<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Plugin\Paginator;

class MemberController extends ActionController {

    public function display_profileAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;

        $member = Member::getInstance();
        // Affichage du genre
        if ($member->sexe == 0) {
            $this->viewParameters['gender'] = '<option value=1 selected="selected">' . $translate->translate("female") . '</option><option value=2>' . $translate->translate("male") . '</option>';
        } else {
            $this->viewParameters['gender'] = '<option value=1>' . $translate->translate("female") . '</option><option value=2 selected="selected">' . $translate->translate("male") . '</option>';
        }

        // Affichage des langues
        $langs = $translate->getList();
        $lang_user = $member->langue;
        $sel_lang = ''; $selected = "";

        for ($i=0; $i<count($langs); $i++) {
            if ($langs[$i] == $lang_user)       {   $selected = ' selected="selected"'; }
            else                                {   $selected = "";                     }
            $sel_lang .= "<option value='" . $langs[$i] . "'" . $selected . ">" . $translate->translate($langs[$i]) . "</option>";
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
        $this->viewParameters['langs']       = $sel_lang;
        $this->viewParameters['login']       = $member->login;
        $this->viewParameters['identity']    = $member->identite;
        $this->viewParameters['mail']        = $member->email;
        $this->viewParameters['avatar']      = $member->avatar;
        if ($member->identite == "Brennan Waco")    {   $this->viewParameters['admin'] = true;   }
        $this->render('members/update_profile');
    }
    
    
    public function valid_profileAction()
    {
        $translate = Registry::get("translate");
        $member = Member::getInstance();
        $err_msg = '';
        
        // Controle si tous les champs sont saisis
        // =======================================
        if (isset($_POST['avatar']) && !empty($_POST['avatar'])) {
            $res = $member->setAvatar($_POST['avatar']);
            if ($res == 0)          {   $err_msg .= $translate->translate("error") . "<br/>";             }
            elseif ($res == -1)     {   $err_msg .= $translate->translate("avatar_invalid") . "<br/>";    }
        }
        if (isset($_POST['login']) && !empty($_POST['login'])) {
            if ($member->setLogin($_POST['login']) == 0)    {   $err_msg = $translate->translate("error") . "<br/>";    }
        }
        if (isset($_POST['identity']) && !empty($_POST['identity'])) {
            $res = $member->setIdentite($_POST['identity']);
            if ($res == 0)          {   $err_msg .= $translate->translate("error") . "<br/>";             }
            elseif ($res == -1)     {   $err_msg .= $translate->translate("identity_invalid") . "<br/>";  }
            elseif ($res == -2)     {   $err_msg .= $translate->translate('identity_exist') . "<br/>";    }
        }
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            if ($member->setPassword($_POST['password']) == 0)          {   $err_msg .= $translate->translate("error") . "<br/>";    }
        }
        if (isset($_POST['gender']) && !empty($_POST['gender'])) {
            if ($member->setSexe($_POST['gender']) == 0)                {   $err_msg .= $translate->translate("error") . "<br/>";    }
        }
        if (isset($_POST['mail']) && !empty($_POST['mail'])) {
            $res = $member->setEmail($_POST['mail']);
            if ($res == 0)          {   $err_msg .= $translate->translate("error") . "<br/>";             }
            elseif ($res == -1)     {   $err_msg .= $translate->translate("mail_invalid") . "<br/>";      }
            elseif ($res == -2)     {   $err_msg .= $translate->translate('mail_exist') . "<br/>";        }
        }
        if (isset($_POST['lang']) || !empty($_POST['lang'])) {
            if ($member->setLangue($_POST['lang']) == 0)                {   $err_msg .= $translate->translate("error") . "<br/>";    }
        }
        
        if (empty($err_msg)) {
            $this->redirect("intranet/index");
        } else {
            $this->display(json_encode(array("status" => 0, "reponse" => '<div class="error" style="height:50px;"><table><tr>' .
                           '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $err_msg . '</span></td>' .
                           '</tr></table></div>')));
        }
    }
    
    
    public function search_membersAction()
    {
        $this->display("<span>toto, test, tata</span>");
    }
    
    
    public function display_contactsgroupsAction()
    {
        $paginator = new Paginator(Member::ListContactsGroups());
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->setCurrentPage($_POST['page']);  }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = Registry::get('translate');
        $tabmini_contactsgroups = '<table class="pagination"><tr class="entete">' .
                                '<td width="150px">' . $translate->translate("group_name") . '</td>' .
                                '<td width="150px">' . $translate->translate("identity") . '</td>' .
                                '</tr>';

        foreach($datas as $row) {
            $tabmini_contactsgroups .= "<tr style='height:25px'>";
            $tabmini_contactsgroups .= "<td>" . utf8_encode($row['Group_name']) . "</td>";
            $tabmini_contactsgroups .= "<td><a onclick=\"Clic('/messaging/compose_message', 'recipient_message=" . $row['IDUser'] . "', 'milieu_milieu')\">" . utf8_encode($row['Identite']) . "</a></td>";
            $tabmini_contactsgroups .= "</tr>";
        }

        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        if ($nLastItem>$paginator->countItems())     {   $nLastItem = $paginator->countItems();   }
        $tabmini_contactsgroups .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/member/display_contactsgroups\', \'&page=' . ($CurPage-1) . '\', \'milieu_gauche\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tabmini_contactsgroups .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/member/display_contactsgroups\', \'&page=' . ($CurPage+1) . '\', \'milieu_gauche\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tabmini_contactsgroups .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_contactsgroups'] = $tabmini_contactsgroups;
        $this->viewParameters['translate'] = $translate;
        $this->render("groups/list_contactsgroups");
    }
}