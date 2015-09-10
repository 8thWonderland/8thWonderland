<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Application\Model\ManageGroups;
use Wonderland\Application\Model\Member;

use Wonderland\Library\Plugin\Paginator;

use Wonderland\Library\Admin\Log;

use Wonderland\Library\Translate;

class GroupController extends ActionController {
    
    // Affichage de la liste des groupes
    // =================================
    public function display_groupsAction()
    {
        $this->viewParameters['list_Allgroups'] = $this->_renderGroups();
        $this->viewParameters['map_coord'] = $this->_renderMapCoord();
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('groups/list_allgroups');
    }
    
    
    // Affichage de la liste des groupes auxquels l'utilisateur appartient
    // ==================================================================
    public function display_groupsmembersAction()
    {
        $translate = Registry::get('translate');
        $list_groups = ManageGroups::display_groupsMember();
        $reponse ='';

        if ($list_groups->num_rows > 0) {
            while ($group = $list_groups->fetch_assoc()) {
                $reponse .= "<tr><td>" . utf8_encode($group['Group_name']) . "</td>" .
                            "<td><div class='bouton' style='margin:3px;'><a onclick=\"Clic('/intranet/index', 'group_id=" . $group['Group_id'] . "', 'body'); return false;\">" .
                                "<span style='color: #dfdfdf;'>" . $translate->translate('btn_enterdesktop') . "</span></a></div></td>";
                $reponse .= "</tr>";
            }
        }
        else {
            $reponse = "<tr><td>" . $translate->translate('no_result') . "</td></tr>";
        }

        $this->viewParameters['list_groups'] = $reponse;
        $this->viewParameters['translate'] = $translate;
        $this->render('groups/list_groups');
    }
    
    
    // Affichage de la liste des membres du groupe
    // ===========================================
    public function display_membersAction()
    {
        $paginator = new Paginator(ManageGroups::display_listMembers());
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->setCurrentPage($_POST['page']);  }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = Registry::get('translate');
        $tabmini_usersgroup =   '<table class="pagination"><tr class="entete">' .
                                '<td>' . $translate->translate("identity") . '</td>' .
                                '<td width="140px">' . $translate->translate("last_connexion") . '</td>' .
                                '</tr>';

        foreach($datas as $key => $row) {
            $tabmini_usersgroup .= "<tr style='height:25px'>";
            $tabmini_usersgroup .= "<td><a onclick=\"Clic('/messaging/compose_message', 'recipient_message=" . $row['IDUser'] . "', 'milieu_milieu')\">" . utf8_encode($row['Identite']) . "</a></td>";
            $tabmini_usersgroup .= "<td>" . substr($row['DerConnexion'], 0, strlen($row['DerConnexion'])-3) . "</td>";
            $tabmini_usersgroup .= "</tr>";
        }

        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        if ($nLastItem>$paginator->countItems())     {   $nLastItem = $paginator->countItems();   }
        $tabmini_usersgroup .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/groups/display_members\', \'&page=' . ($CurPage-1) . '\', \'md_section2\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tabmini_usersgroup .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/groups/display_members\', \'&page=' . ($CurPage+1) . '\', \'md_section2\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tabmini_usersgroup .= $next . '</td></tr></table>';
        
        $this->viewParameters['list_membersgroup'] = $tabmini_usersgroup;
        $this->viewParameters['translate'] = $translate;
        $this->render('groups/list_membersgroup');
    }
                
    
    // Affichage du panneau de gestion du groupe
    // =========================================
    public function display_managegroupsAction()
    {
        $this->viewParameters['translate'] = Registry::get("translate");

        $list_members = ManageGroups::display_listMembersContact();
        $select = "<option></option>";
        for ($i=0; $i<count($list_members); $i++) {
            $select .= "<option value='" . $list_members[$i]['IDUser'] . "'>" . $list_members[$i]['Identite'] . "</option>";
        }
        $this->viewParameters['select_contactsgroup'] = $select;
        $this->render('groups/manage_groups');
    }
    
    
    // Affichage du calendrier
    // =======================
    public function display_calendarAction()
    {
        $this->viewParameters['translate'] = Registry::get('translate');
        $this->render('admin/dev_inprogress');
    }
        
    
    // Affichage de l'annuaire
    // =======================
    public function display_adressbookAction()
    {
        $this->viewParameters['list_users'] = $this->_renderUsers();
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('members/list_users');
    }
            
    
    // Affichage des favoris
    // =====================
    public function display_bookmarkAction()
    {
        $this->viewParameters['translate'] = Registry::get('translate');
        $this->render('admin/dev_inprogress');
    }
    
    
    public function quit_desktopAction()
    {
        Registry::delete("desktop");
        Registry::delete("search_users");
        $this->redirect("intranet/index");
    }
    
    
    // Changement du contact du groupe
    // ===============================
    public function change_contactgroupsAction()
    {
        $translate = Registry::get('translate');
        $member = Member::getInstance();
        
        if (!isset($_POST['sel_contactgroups']) || intval($_POST['sel_contactgroups']) == 0) {
            $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->translate('error') . '</span></td>' .
                          '</tr></table></div>');
            
            // log d'échec de mise à jour
            $db_log = new Log("db");
            $db_log->log("Echec du changement de CG par " . $member->identite . " (id_user inconnu : " . $_POST['sel_contactgroups'] . ")", Log::ERR);
        } else {
            $res = ManageGroups::change_contact($_POST['sel_contactgroups']);
            if ($res ==0) {
                $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->translate('error') . '</span></td>' .
                          '</tr></table></div>');
                
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec du changement de CG par " . $member->identite . " (id_user=" . $_POST['sel_contactgroups'] . ")", Log::ERR);
            } else {
                $desktop = Registry::get("desktop");
                $this->display("<script type='text/javascript'>window.onload=Clic('/intranet/index', '" . $desktop . "', 'body');</script>");
                
                // log de mise à jour
                $db_log = new Log("db");
                $db_log->log("Changement de CG par " . $member->identite . " (id_user=" . $_POST['sel_contactgroups'] . ")", Log::INFO);
            }
        }
        
        $this->viewParameters['translate'] = $translate;
    }
    
    
    // Mise en page tabulaire de la liste des groupes à afficher
    // =========================================================
    protected function _renderGroups()
    {
        $paginator = new Paginator(managegroups::display_groups());
        $paginator->setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->setCurrentPage($_POST['page']);  }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = Registry::get("translate");
        
        $tab_groups = '<table id="pagination_motions" class="pagination"><tr class="entete">' .
                      '<td>' . $translate->translate("group_name") . '</td>' .
                      '<td>' . $translate->translate("group_description") . '</td>' .
                      '<td>' . $translate->translate("group_contact") . '</td>' .
                      '<td>' . $translate->translate("group_datecreation") . '</td>' .
                      '<td>' . $translate->translate("group_type") . '</td>' .
                      '<td>' . $translate->translate("group_nbmembers") . '</td>' .
                      '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_groups .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "Group_id" && $key != "Longitude" && $key != "Latitude") {
                    $tab_groups .= "<td>" . $this->_filterGroups($key, $value) . "</td>";
                }
            }
            $tab_groups .= "<td align='center'>" . ManageGroups::NbMembers($row['Group_id']) . "</td>";
            $tab_groups .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        if ($nLastItem>$paginator->countItems())     {   $nLastItem = $paginator->countItems();   }
        $tab_groups .= '<tr class="pied"><td align="left" colspan="3">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/groups/display_groups\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_groups .= '<td colspan="3" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_groups .= '<a onclick="Clic(\'/groups/display_groups\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_groups .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/groups/display_groups\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tab_groups .= $next . '</td></tr></table>';
        
        return $tab_groups;
    }
        
    
    // Mise en page tabulaire de la liste des membres
    // ==============================================
    protected function _renderUsers()
    {
        $search = $_POST;
        if (isset($_POST['page']))      {   $search = Registry::get("search_users");     }
        else                            {   Registry::set("search_users", $_POST);       }
        
        $paginator = new Paginator(Member::ListMembers($search));
        $paginator->setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->setCurrentPage($_POST['page']);  }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = Registry::get("translate");
        
        $list_groups = ManageGroups::display_groups();
        $this->viewParameters['select_groups'] = "<option></options>";
        $i=0;
        for ($i=0; $i<count($list_groups); $i++) {
            $this->viewParameters['select_groups'] .= "<option value='" . $list_groups[$i]['Group_id'] . "'>" . utf8_encode($list_groups[$i]['Group_name']) . "</option>";
        }
        
        $tab_users = '<table id="pagination_users" class="pagination"><tr class="entete">' .
                    '<td width="50px">' . $translate->translate("avatar") . '</td>' .
                    '<td width="200px">' . $translate->translate("identity") . '</td>' .
                    '<td width="50px">' . $translate->translate("gender") . '</td>' .
                    '<td width="200px">' . $translate->translate("mail") . '</td>' .
                    '<td width="50px">' . $translate->translate("lang") . '</td>' .
                    '<td width="150px">' . $translate->translate("country") . '</td>' .
                    '<td width="150px">' . $translate->translate("region") . '</td>' .
                    '<td width="150px">' . $translate->translate("last_connexion") . '</td>' .
                    '<td width="150px">' . $translate->translate("subscription") . '</td>' .
                    '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_users .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "iduser" && $key != "identite") {
                    $tab_users .= "<td>" . $this->_filterUsers($key, $value) . "</td>";
                } elseif ($key == "identite") {
                    $tab_users .= "<td><a onclick=\"Clic('/messaging/compose_message', 'recipient_message=" . $row['iduser'] . "', 'milieu_milieu')\">" . 
                            utf8_encode($row['identite']) . "</a></td>";
                }
            }
            $tab_users .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        if ($nLastItem>$paginator->countItems())     {   $nLastItem = $paginator->countItems();   }
        $tab_users .= '<tr class="pied"><td colspan="6" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/groups/display_adressbook\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_users .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_users .= '<a onclick="Clic(\'/groups/display_adressbook\', \'page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_users .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/groups/display_adressbook\', \'page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $tab_users .= $next . '</td></tr></table>';
        
        return $tab_users;
    }
    
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterGroups($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "group_name":
                $value = utf8_encode($value);
                break;
            
            case "description":
                $value = html_entity_decode($value);
                break;
            
            case "group_type_description":
                $value = html_entity_decode($value);
                break;
            
            case "creation":
                $value = $value;
        }
        
        return $value;
    }
    
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterUsers($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "avatar":
                $value = '<img width="50" alt="Avatar" src="' . $value . '">';
                break;
            
            case "identite":
                $value = utf8_encode($value);
                break;
            
            case "sexe":
                $value = ($value==1?'M':'F');
                break;
            
            case "pays":
                $db = Registry::get('db');
                $member = Member::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT " . $lang . " FROM country WHERE code = '" . $value . "' LIMIT 1");
                if (count($res) >0)    {   $value = $res[0][$lang];                }
                else                
                {
                    $translate = Translate::getInstance();
                    $value = $translate->translate("unknown");
                }
                break;
            
            case "region":
                $db = Registry::get('db');
                $member = Member::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT Name FROM regions WHERE Region_id = " . $value . " LIMIT 1");
                if (count($res) >0 && $value >0)    {   $value = utf8_encode($res[0]['Name']);        }
                else                
                {
                    $translate = Translate::getInstance();
                    $value = $translate->translate("unknown");
                }
                break;
            
            case "derconnexion":
                $value = substr($value, 0, strlen($value)-3);
                break;
        }
        
        return $value;
    }
        
    
    protected function _renderMapCoord()
    {
     	/*	
        var regions = [
                ['Alsace', 48.30, 7.30],
                ['Aquitaine', 44.35, 0.01],
                ['Auvergne', 45.48, 3.28],
                ['Bourgogne', 47.1451, 4.0905],
                ['Bretagne', 48.3, -2.92], 
                ['Région Centre', 47.30, 1.45],
                ['Champagne-Ardenne', 49.0, 4.5],
                ['Corse', 42.5, 9.3],
                ['Franche-Comté', 47.0, 6.0],
                ['Île-de-France', 48.76, 2.45],
                ['Languedoc-Roussillon', 43.40, 3.10],
                ['Limousin', 45.9, 1.7],
                ['Lorraine', 49.00, 6.00],
                ['Midi-Pyrénées', 43.96, 1.6],
                ['Nord-Pas-de-Calais', 50.28, 2.43],
                ['Basse-Normandie', 49.0, -0.5],
                ['Haute-Normandie', 49.3, 1.0],
                ['Pays de la Loire', 47.7, -0.5],
                ['Picardie', 49.3, 2.5],
                ['Poitou-Charentes', 46.210, -0.132],
                ["Provence-Alpes-Côte d'Azur", 44.16, 6.16],
                ['Rhône-Alpes', 45.5, 5.53],
                ['Guadeloupe', 16.25, -61.583333],
                ['Guyane', 4.229167, -52.988889],
                ['Martinique', 14.666667, -61.0],
                ['Mayotte', -12.80343, 45.16308],
                ['La Réunion', -21.15, 55.5],
                ['Québec', 46.789, -71.213],
                ['Wallonie', 50.412, 5.026],
                ['Suisse', 46.702, 7.141],
                ['Flandre', 51.0, 4.7],
                ['Bruxelles',50.843237,4.362946]
                ];
        */
        $render = "";
        foreach(ManageGroups::display_groups_regions() as $key => $row)
        {
            if($row['Longitude'] !="" && $row['Latitude'] !="")
            {
                $render .= '["'.htmlentities($row['Group_name'], ENT_QUOTES).'", '.$row['Longitude'].", ".$row['Latitude'].", ". ManageGroups::NbMembers($row['Group_id'])."],\n";
            }
        }
        return "var regions = [".substr($render, 0, -2)."];";
     }
}