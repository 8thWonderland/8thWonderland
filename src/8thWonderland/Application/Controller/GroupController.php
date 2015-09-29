<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Application\Model\ManageGroups;
use Wonderland\Application\Model\Member;

use Wonderland\Library\Admin\Log;

class GroupController extends ActionController {
    public function displayGroupsAction() {
        $this->viewParameters['list_Allgroups'] = $this->renderGroups();
        $this->viewParameters['map_coord'] = $this->renderMapCoord();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('groups/list_allgroups');
    }
    
    public function displayGroupsMembersAction() {
        if (($id = $this->application->get('session')->get('__id__')) === null) {
            $this->redirect('Index/index');
        }
        
        $translate = $this->application->get('translate');
        $list_groups = $this->application->get('group_manager')->getMemberGroups($id);
        $response = '';

        if ($list_groups->num_rows > 0) {
            while ($group = $list_groups->fetch_assoc()) {
                $response .=
                    "<tr><td>" . utf8_encode($group['Group_name']) . "</td>" .
                    "<td><div class='bouton' style='margin:3px;'><a onclick=\"Clic('/intranet/index', 'group_id={$group['Group_id']}', 'body'); return false;\">" .
                    "<span style='color: #dfdfdf;'>{$translate->translate('btn_enterdesktop')}</span></a></div></td></tr>"
                ;
            }
        } else {
            $response = "<tr><td>{$translate->translate('no_result')}</td></tr>";
        }

        $this->viewParameters['list_groups'] = $response;
        $this->viewParameters['translate'] = $translate;
        $this->render('groups/list_groups');
    }
    
    public function displayMembersAction() {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('group_manager')->getGroupMembers($this->application->get('session')->get('desktop')));
        $paginator->setItemsPerPage(15);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        
        $translate = $this->application->get('translate');
        $tabmini_usersgroup =   
            '<table class="pagination"><tr class="entete">' .
            '<td>' . $translate->translate('identity') . '</td>' .
            '<td width="140px">' . $translate->translate("last_connexion") . '</td></tr>'
        ;

        foreach($datas as $row) {
            $tabmini_usersgroup .=
                "<tr style='height:25px'>".
                "<td><a onclick=\"Clic('/messaging/compose_message', 'recipient_message={$row['IDUser']}', 'milieu_milieu')\">" . utf8_encode($row['Identite']) . "</a></td>" .
                "<td>" . substr($row['DerConnexion'], 0, strlen($row['DerConnexion'])-3) . "</td></tr>"
            ;
        }
        $itemsPerPage = $paginator->getItemsPerPage();
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $itemsPerPage) + 1;
        $nLastItem = ($CurPage * $itemsPerPage);
        
        $nbItems = $paginator->countItems();
        
        if ($nLastItem > $nbItems) {
            $nLastItem = $nbItems;
        }
        $tabmini_usersgroup .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/groups/display_members\', \'&page=' . ($CurPage-1) . '\', \'md_section2\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tabmini_usersgroup .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        // Bouton suivant
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/groups/display_members\', \'&page=' . ($CurPage + 1) . '\', \'md_section2\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        $this->viewParameters['list_membersgroup'] = $tabmini_usersgroup . $next . '</td></tr></table>';
        $this->viewParameters['translate'] = $translate;
        $this->render('groups/list_membersgroup');
    }
    
    public function displayManageGroupsAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');

        $membersList = $this->application->get('group_manager')->getGroupMembers($this->application->get('session')->get('desktop'));
        $select = '<option></option>';
        $nbMembers = count($membersList);
        for ($i = 0; $i < $nbMembers; ++$i) {
            $select .= "<option value='{$membersList[$i]['IDUser']}'>{$membersList[$i]['Identite']}</option>";
        }
        $this->viewParameters['select_contactsgroup'] = $select;
        $this->render('groups/manage_groups');
    }
    
    public function displayCalendarAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/dev_inprogress');
    }
    
    public function displayAdressbookAction() {
        $this->viewParameters['list_users'] = $this->renderUsers();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('members/list_users');
    }
    
    public function displayBookmarkAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/dev_inprogress');
    }
    
    
    public function quitDesktopAction() {
        $session = $this->application->get('session');
        $session->delete('desktop');
        $session->delete('search_users');
        $this->redirect('intranet/index');
    }
    
    public function changeContactGroupsAction() {
        $translate = $this->application->get('translate');
        $dbLogger = $this->application->get('logger');
        $dbLogger->setWriter('db');
        $session = $this->application->get('session');
        $member = $this->application->get('member_manager')->getMember($session->get('__id__'));
        
        if (!isset($_POST['sel_contactgroups']) || intval($_POST['sel_contactgroups']) === 0) {
            $this->display(
                '<div class="error" style="height:25px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('error') . '</span></td>' .
                '</tr></table></div>'
            );
            
            $dbLogger->log("Echec du changement de CG par {$member->getIdentity()} (id_user inconnu : {$_POST['sel_contactgroups']})", Log::ERR);
        } else {
            $desktop = $session->get('desktop');
            $res = $this->application->get('group_manager')->updateContact($desktop, $_POST['sel_contactgroups']);
            if ($res === 0) {
                $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->translate('error') . '</span></td>' .
                          '</tr></table></div>');
                
                $dbLogger->log("Echec du changement de CG par {$member->getIdentity()} (id_user = {$_POST['sel_contactgroups']})", Log::ERR);
            } else {
                $this->display("<script type='text/javascript'>window.onload=Clic('/intranet/index', '$desktop', 'body');</script>");
                
                $dbLogger->log("Changement de CG par {$member->getIdentity()} (id_user = {$_POST['sel_contactgroups']})", Log::INFO);
            }
        }
        
        $this->viewParameters['translate'] = $translate;
    }
    
    /**
     * @return string
     */
    protected function renderGroups() {
        $groupManager = $this->application->get('group_manager');
        $paginator = $this->application->get('paginator');
        $paginator->setData($groupManager->getGroups());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translate');
        
        $tab_groups = 
            '<table id="pagination_motions" class="pagination"><tr class="entete">' .
            '<td>' . $translate->translate('group_name') . '</td>' .
            '<td>' . $translate->translate('group_description') . '</td>' .
            '<td>' . $translate->translate('group_contact') . '</td>' .
            '<td>' . $translate->translate('group_datecreation') . '</td>' .
            '<td>' . $translate->translate('group_type') . '</td>' .
            '<td>' . $translate->translate('group_nbmembers') . '</td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_groups .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key !== 'Group_id' && $key !== 'Longitude' && $key !== 'Latitude') {
                    $tab_groups .= "<td>{$this->filterGroups($key, $value)}</td>";
                }
            }
            $tab_groups .= "<td align='center'>{$groupManager->countMembers($row['Group_id'])}</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage()) + 1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        if ($nLastItem > $paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_groups .= '<tr class="pied"><td align="left" colspan="3">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/Group/displayGroups\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_groups .= '<td colspan="3" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_groups .=
                ($page != $CurPage)
                ? '<a onclick="Clic(\'/Group/displayGroups\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : "<b>$page</b> | "
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/Group/displayGroups\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        return $tab_groups . $next . '</td></tr></table>';
    }
    
    /**
     * @return string
     */
    protected function renderUsers() {
        $session = $this->application->get('session');
        $memberManager = $this->application->get('member_manager');
        $search = $_POST;
        if (isset($_POST['page'])) {
            $search = $session->get('search_users');
        } else {
            $session->set('search_users', $_POST);
        }
        $paginator = $this->application->get('paginator');
        $paginator->setData(Member::ListMembers($search));
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translate');
        
        $list_groups = ManageGroups::display_groups();
        $this->viewParameters['select_groups'] = '<option></options>';
        $nbGroups = count($list_groups);
        for ($i = 0; $i < $nbGroups; ++$i) {
            $this->viewParameters['select_groups'] .= "<option value='{$list_groups[$i]['Group_id']}'>" . utf8_encode($list_groups[$i]['Group_name']) . "</option>";
        }
        
        $tab_users = 
            '<table id="pagination_users" class="pagination"><tr class="entete">' .
            '<td width="50px">' . $translate->translate('avatar') . '</td>' .
            '<td width="200px">' . $translate->translate('identity') . '</td>' .
            '<td width="50px">' . $translate->translate('gender') . '</td>' .
            '<td width="200px">' . $translate->translate('mail') . '</td>' .
            '<td width="50px">' . $translate->translate('lang') . '</td>' .
            '<td width="150px">' . $translate->translate('country') . '</td>' .
            '<td width="150px">' . $translate->translate('region') . '</td>' .
            '<td width="150px">' . $translate->translate('last_connexion') . '</td>' .
            '<td width="150px">' . $translate->translate('subscription') . '</td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_users .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                $tab_users .= 
                    ($key !== 'id' && $key !== 'identity')
                    ? "<td>{$this->filterUsers($key, $value)}</td>"
                    : "<td><a onclick=\"Clic('/messaging/compose_message', 'recipient_message=" . $row['id'] . "', 'milieu_milieu')\">" . utf8_encode($row['identity']) . "</a></td>"
                ;
            }
            $tab_users .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        
        if ($nLastItem>$paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
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
        
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page=$start; $page < $end + 1; ++$page) {
            $tab_users .= 
                ($page != $CurPage)
                ? '<a onclick="Clic(\'/Group/displayAddressBook\', \'page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/groups/display_adressbook\', \'page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        return $tab_users . $next . '</td></tr></table>';
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterGroups($key, $value) {
        switch(strtolower($key)) {
            case 'group_name':
                return utf8_encode($value);
                
            case 'description':
                return html_entity_decode($value);
            
            case 'group_type_description':
                return html_entity_decode($value);
        }
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterUsers($key, $value) {
        switch(strtolower($key)) {
            case 'avatar':
                $value = '<img width="50" alt="Avatar" src="' . $value . '">';
                break;
            
            case 'identity':
                $value = utf8_encode($value);
                break;
            
            case 'gender':
                $value = ($value === 1 ? 'M' : 'F');
                break;
            
            case 'country':
                $member = $this->application->get('member_manager')->getMember($this->application->get('session')->get('__id__'));
                $lang = $member->getLanguage();
                $res = $this->application->get('mysqli')->select("SELECT $lang FROM country WHERE code = '$value' LIMIT 1");
                $value =
                    (count($res) > 0)
                    ? $res[0][$lang]
                    : $this->application->get('translate')->translate("unknown")
                ;
                break;
            
            case 'region':
                $member = $this->application->get('member_manager')->getMember($this->application->get('session')->get('__id__'));
                $lang = $member->langue;
                $res = $this->application->get('mysqli')->select("SELECT Name FROM regions WHERE Region_id = $value LIMIT 1");
                $value =
                    (count($res) > 0 && $value > 0)
                    ? utf8_encode($res[0]['Name'])
                    : $this->application->get('translate')->translate('unknown')
                ;
                break;
            
            case 'last_connected_at':
                $value = substr($value, 0, strlen($value)-3);
                break;
        }
        return $value;
    }
    
    protected function renderMapCoord() {
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
        $render = '';
        $regionalGroups = $this->application->get('group_manager')->getRegionalGroups();
        foreach($regionalGroups as $row) {
            if(!empty($row['Longitude']) && !empty($row['Latitude'])) {
                $render .= '["'.htmlentities($row['Group_name'], ENT_QUOTES).'", '.$row['Longitude'].", ".$row['Latitude'].", ". ManageGroups::NbMembers($row['Group_id'])."],\n";
            }
        }
        return 'var regions = ['.substr($render, 0, -2).'];';
    }
}