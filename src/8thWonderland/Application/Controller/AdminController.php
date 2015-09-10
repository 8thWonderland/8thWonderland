<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Plugin\Paginator;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Ovh;

/**
 * Controleur des administreteurs (développeurs)
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/
class AdminController extends ActionController {   
    
    public function displayConsoleAction()
    {
        // affichage du profil
        $member = Member::getInstance();
        $this->viewParameters['identity'] = $member->identite;
        $this->viewParameters['avatar'] = $member->avatar;
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/console');
    }
    
    
    public function quitConsoleAction() {
        Registry::delete("desktop");
        
        // Journal de log
        $member = Member::getInstance();
        $db_log = new Log("db");
        $db_log->log($member->identite . " quitte la console d'administration.", Log::INFO);
        
        $this->redirect('intranet/index');
    }
    
    
    public function displayLogsAction() {
        $this->viewParameters['list_logs'] = $this->renderLogs();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/logs');
    }
        
    
    public function displayUsersAction() {
        $this->viewParameters['list_users'] = $this->renderUsers();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/users');
    }
            
    
    public function displayGroupsAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/dev_inprogress');
    }
                
    
    public function displayServerAction() {
        $translate = $this->application->get('translate');
        $cronsList = (new Ovh())->list_cron();
        $this->viewParameters['crons'] = 
            (isset($cronsList))
            ? $this->renderCrons($cronsList)
            : 
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('connexion_nok') . '</span></td>' .
                '</tr></table></div>'
        ;
        $this->viewParameters['translate'] = $translate;
        $this->render('admin/console_ovh');
    }
    
    
    public function displayCreateCronAction() {
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('admin/create_cron');
    }
    
    public function displayStatsCountryAction() {
        $db = $this->application->get('mysqli');
        $translate = $this->application->get('translate');
        $desktop = Registry::get('desktop');
        if (isset($desktop) && $desktop == 1)   {
            $member = Member::getInstance();
            $lang = $member->langue;
            $regionUnknown = $db->select("SELECT $lang, Pays FROM Utilisateurs, country WHERE Region = -1 AND Pays=code");
            $this->viewParameters['stats_regions_other'] = "<table><tr><td>" . $translate->translate('stats_region_unknown') . "</td></tr>";
            for ($i=0; $i<count($regionUnknown); $i++) {
                $this->viewParameters['stats_regions_other'] .= "<tr><td>- " . $regionUnknown[$i][$lang] . " (" . $regionUnknown[$i]['Pays'] . ")</td></tr>";
            }
            $this->viewParameters['stats_regions_other'] .= "</table>";
            $this->viewParameters['desktop'] = 1;
        }
        $regions_ok = $db->count('Utilisateurs', ' WHERE Region > 0');
        
        $this->viewParameters['stats_members'] = Member::Nb_Members();
        $this->viewParameters['stats_members_actives'] = Member::Nb_ActivesMembers();
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['stats_regions_ok'] = $regions_ok;
        
        $this->render("informations/stats_country");
    }
    
    public function addCronAction() {
        $translate = $this->application->get('translate');
        $err_msg = '';
        
        if (empty($_POST['cron_file']) || empty($_POST['cron_desc']) || empty($_POST['cron_day']) || count($_POST) < 7)
        {
            $err_msg = $translate->translate('fields_empty');
        } else {
            $id_cron = (new myovh())->addCron($_POST);
            switch ($id_cron) {
                case -1:
                    $err_msg = $translate->translate('file_notfound');
                    break;
                
                case 0:
                    $err_msg = $translate->translate('error');
                    break;
                
                case -2:
                    $err_msg = $translate->translate('connexion_nok');
            }
        }
        
        if (!empty($err_msg)) {
            $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $err_msg . '</span></td>' .
                          '</tr></table></div>');
        } else {
            $this->display("<script type='text/javascript'>window.onload=Clic('/admin/display_server', '', 'milieu_milieu');</script>");
        }
    }
    
    public function deleteCronAction() {
        $translate = $this->application->get('translate');
        $res = (new myovh())->deleteCron($_POST['cronid'], $_POST['crondesc']);
        if (isset($res)) {
            if ($res == false) {
                $this->display('<div class="error" style="height:25px;"><table><tr>' .
                              '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                              '<td><span style="font-size: 15px;">' . $translate->translate('error') . '</span></td>' .
                              '</tr></table></div>');
            } else {
                $this->display("<script type='text/javascript'>window.onload=Clic('/admin/display_server', '', 'milieu_milieu');</script>");
            }
        } else {
            $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->translate('connexion_nok') . '</span></td>' .
                          '</tr></table></div>');
        }

        $this->viewParameters['translate'] = $translate;
    }
    
    public function editCronAction() {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('admin/dev_inprogress');
    }
    
    /**
     * @param string $cron
     * @return string
     */
    protected function renderCrons($cron) {
        $paginator = new Paginator($cron);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translate');
                
        $tab_crons =
            '<table id="pagination_motions" class="pagination"><tr class="entete">' .
            '<td width="20px">' . $translate->translate('cron_id') . '</td>' .
            '<td>' . $translate->translate('cron_file') . '</td>' .
            '<td>' . $translate->translate('cron_description') . '</td>' .
            '<td width="40px">' . $translate->translate('cron_active') . '</td>' .
            '<td></td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_crons .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != 'email') {
                    $tab_crons .= "<td>{$this->filterCrons($key, $value)}</td>";
                }
            }
            $array = get_object_vars($row);
            $tab_crons .=
                "<td><a onclick=\"Clic('/admin/edit_cron', 'idcron=" . $array['id'] . "', 'server_infoaction'); return false;\">" .
                "<img width='24' src='" . ICO_PATH . "32x32/edit.png'/></a>" .
                "<a onclick=\"Clic('/admin/delete_cron', 'cronid=" . $array['id'] . "&crondesc=" . $array['desc'] . "', 'server_infoaction'); return false;\">" .
                "<img width='24' src='" . ICO_PATH . "32x32/delete.png'/></a></td></tr>"
            ;
        }
        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);
        
        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_crons .= '<tr class="pied"><td colspan="2" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/admin/display_server\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_crons .= '<td colspan="4" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_crons .=
                ($page != $CurPage)
                ? '<a onclick="Clic(\'/admin/display_server\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : $tab_crons .= '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/admin/display_server\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        
        return 
            $tab_crons . $next . '</td></tr></table>' .
            "<div class='bouton' style='width:120px;'><a onclick=\"Clic('/admin/display_createcron', '', 'milieu_milieu'); return false;\">" .
            "<span style='color: #dfdfdf;'>" . $translate->translate('btn_addcron') . "</span></a></div>"
        ;
    }
    
    /**
     * @return string
     */
    protected function renderLogs()
    {
        $paginator = new Paginator(Log::display_dblogs());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        
        $translate = $this->application->get('translate');
        
        $tab_logs =
            '<table id="pagination_motions" class="pagination"><tr class="entete">' .
            '<td width="20px">' . $translate->translate("logs_level") . '</td>' .
            '<td width="150px">' . $translate->translate("logs_date") . '</td>' .
            '<td>' . $translate->translate("logs_description") . '</td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_logs .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key !== 'Motion_id') {
                    $tab_logs .= "<td>{$this->filterLogs($key, $value)}</td>";
                }
            }
            $tab_logs .= "</tr>";
        }
        
        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);
        
        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_logs .= '<tr class="pied"><td colspan="2" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_logs .= '<td style="padding-right:15px;" align="right">' . $previous . ' | ';
        
        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;
        
        if ($start<1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page = $start; $page<$end + 1; ++$page) {
            $tab_logs .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        return $tab_logs . $next . '</td></tr></table>';
    }
        
    /**
     * @return string
     */
    protected function renderUsers() {
        $paginator = new Paginator(members::ListMembers());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        
        $translate = $this->application->get('translate');
        
        $tab_users =
            '<table id="pagination_users" class="pagination"><tr class="entete">' .
            '<td width="50px">' . $translate->translate("avatar") . '</td>' .
            '<td width="200px">' . $translate->translate("identity") . '</td>' .
            '<td width="50px">' . $translate->translate("gender") . '</td>' .
            '<td width="200px">' . $translate->translate("mail") . '</td>' .
            '<td width="50px">' . $translate->translate("lang") . '</td>' .
            '<td width="150px">' . $translate->translate("country") . '</td>' .
            '<td width="150px">' . $translate->translate("region") . '</td>' .
            '<td width="150px">' . $translate->translate("last_connexion") . '</td>' .
            '<td width="150px">' . $translate->translate("subscription") . '</td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_users .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "iduser") {
                    $tab_users .= "<td>" . $this->filterUsers($key, $value) . "</td>";
                }
            }
            $tab_users .= "</tr>";
        }
        
        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);
        
        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_users .= '<tr class="pied"><td colspan="6" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/admin/display_users\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_users .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        
        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;
        
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_users .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'/admin/display_users\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : $tab_users .= '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/admin/display_users\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        return $tab_users . $next . '</td></tr></table>';
    }
        
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function filterLogs($key, $value)
    {
        if(strtolower($key) === 'label_key') {
            $value = $this->application->get('translate')->translate($value);
        }
        return $value;
    }
            
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function filterUsers($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case 'avatar':
                $value = '<img width="50" alt="Avatar" src="' . $value . '">';
                break;
            
            case 'identite':
                $value = utf8_encode($value);
                break;
            
            case 'sexe':
                $value = ($value==1?'M':'F');
                break;
            
            case 'pays':
                $db = $this->application->get('mysqli');
                $member = Member::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT $lang FROM country WHERE code = '$value' LIMIT 1");
                $value =
                    (count($res) > 0)
                    ? $res[0][$lang]
                    : $this->application->get('translate')->translate('unknown')
                ;
                break;
            
            case 'region':
                $db = $this->application->get('db');
                $member = Member::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT Name FROM regions WHERE Region_id = $value LIMIT 1");
                $value =
                    (count($res) >0 && $value >0)
                    ? utf8_encode($res[0]['Name'])
                    : $this->application->get('translate')->translate('unknown')
                ;
                break;
        }
        
        return $value;
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterCrons($key, $value) {
        switch(strtolower($key)) {
            case 'desc':
                return utf8_encode($value);
            case 'enabled':
                return
                    ($value === 1)
                    ? '<img width="16" alt="active" src="' . ICO_PATH . '32x32/valid.png">'
                    : '<img width="16" alt="inactive" src="' . ICO_PATH . '32x32/echec.png">'
                ;
        }
    }
}