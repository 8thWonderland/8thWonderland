<?php
/**
 * Controleur des administreteurs (développeurs)
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/


class admin extends controllers_action {   
    
    public function display_consoleAction()
    {
        // affichage du profil
        $member = members::getInstance();
        $this->_view['identity'] = $member->identite;
        $this->_view['avatar'] = $member->avatar;
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/console');
    }
    
    
    public function quit_consoleAction()
    {
        memory_registry::delete("desktop");
        
        // Journal de log
        $member = members::getInstance();
        $db_log = new admin_logs("db");
        $db_log->log($member->identite . " quitte la console d'administration.", admin_logs::INFO);
        
        $this->redirect("intranet/index");
    }
    
    
    public function display_logsAction()
    {
        $this->_view['list_logs'] = $this->_renderLogs();
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/logs');
    }
        
    
    public function display_usersAction()
    {
        $this->_view['list_users'] = $this->_renderUsers();
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/users');
    }
            
    
    public function display_groupsAction()
    {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/dev_inprogress');
    }
                
    
    public function display_serverAction()
    {
        $translate = memory_registry::get("translate");
        $ovh = new myovh();
        $list_cron = $ovh->list_cron();
        if (isset($list_cron)) {
            $this->_view['crons'] = $this->_renderCrons($list_cron);
        } else {
            $this->_view['crons'] = '<div class="error" style="height:50px;"><table><tr>' .
                                      '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                                      '<td><span style="font-size: 15px;">' . $translate->msg('connexion_nok') . '</span></td>' .
                                      '</tr></table></div>';
        }
        
        $this->_view['translate'] = $translate;
        $this->render('admin/console_ovh');
    }
    
    
    public function display_createcronAction()
    {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/create_cron');
    }
    
    
    public function display_statsCountryAction()
    {
        $db = memory_registry::get('db');
        $translate = memory_registry::get("translate");
        $desktop = memory_registry::get("desktop");
        if (isset($desktop) && $desktop == 1)   {
            $member = members::getInstance();
            $lang = $member->langue;
            $regionUnknown = $db->select("SELECT " . $lang . ", Pays FROM Utilisateurs, country WHERE Region = -1 AND Pays=code");
            $this->_view['stats_regions_other'] = "<table><tr><td>" . $translate->msg('stats_region_unknown') . "</td></tr>";
            for ($i=0; $i<count($regionUnknown); $i++) {
                $this->_view['stats_regions_other'] .= "<tr><td>- " . $regionUnknown[$i][$lang] . " (" . $regionUnknown[$i]['Pays'] . ")</td></tr>";
            }
            $this->_view['stats_regions_other'] .= "</table>";
            $this->_view['desktop'] = 1;
        }
        $regions_ok = $db->count("Utilisateurs", " WHERE Region > 0");
        
        
        $this->_view['stats_members'] = members::Nb_Members();
        $this->_view['stats_members_actives'] = members::Nb_ActivesMembers();
        $this->_view['translate'] = $translate;
        $this->_view['stats_regions_ok'] = $regions_ok;
        
        $this->render("informations/stats_country");
    }
    
    
    // Ajout d'une tache CRON
    // ======================
    public function add_cronAction()
    {
        $translate = memory_registry::get("translate");
        $err_msg = "";
        
        if (!isset($_POST['cron_file']) || empty($_POST['cron_file']) || !isset($_POST['cron_desc']) || empty($_POST['cron_desc']) || 
            !isset($_POST['cron_day']) || empty($_POST['cron_day']) || count($_POST) <7)
        {
            $err_msg = $translate->msg('fields_empty');
        } else {
            $ovh = new myovh();
            $id_cron = $ovh->add_cron($_POST);
            switch ($id_cron) {
                case -1:
                    $err_msg = $translate->msg('file_notfound');
                    break;
                
                case 0:
                    $err_msg = $translate->msg('error');
                    break;
                
                case -2:
                    $err_msg = $translate->msg('connexion_nok');
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
    
    
    // Suppression d'une tache cron
    // ============================
    public function delete_cronAction()
    {
        $translate = memory_registry::get("translate");
        $ovh = new myovh();
        $res = $ovh->delete_cron($_POST['cronid'], $_POST['crondesc']);
        if (isset($res)) {
            if ($res == false) {
                $this->display('<div class="error" style="height:25px;"><table><tr>' .
                              '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                              '<td><span style="font-size: 15px;">' . $translate->msg('error') . '</span></td>' .
                              '</tr></table></div>');
            } else {
                $this->display("<script type='text/javascript'>window.onload=Clic('/admin/display_server', '', 'milieu_milieu');</script>");
            }
        } else {
            $this->display('<div class="error" style="height:25px;"><table><tr>' .
                          '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:24px;"/></td>' .
                          '<td><span style="font-size: 15px;">' . $translate->msg('connexion_nok') . '</span></td>' .
                          '</tr></table></div>');
        }

        $this->_view['translate'] = $translate;
    }
    
    
    // Modification d'une tache cron
    // =============================
    public function edit_cronAction()
    {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('admin/dev_inprogress');
    }
    
    
    // Mise en page tabulaire de la liste des logs
    // ===========================================
    protected function _renderCrons($cron)
    {
        $paginator = new plugins_paginator($cron);
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = memory_registry::get("translate");
                
        $tab_crons = '<table id="pagination_motions" class="pagination"><tr class="entete">' .
                    '<td width="20px">' . $translate->msg("cron_id") . '</td>' .
                    '<td>' . $translate->msg("cron_file") . '</td>' .
                    '<td>' . $translate->msg("cron_description") . '</td>' .
                    '<td width="40px">' . $translate->msg("cron_active") . '</td>' .
                    '<td></td>' .
                    '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_crons .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "email") {
                    $tab_crons .= "<td>" . $this->_filterCrons($key, $value) . "</td>";
                }
            }
            $array = get_object_vars($row);
            $tab_crons .= "<td><a onclick=\"Clic('/admin/edit_cron', 'idcron=" . $array['id'] . "', 'server_infoaction'); return false;\">" .
                          "<img width='24' src='" . ICO_PATH . "32x32/edit.png'/></a>" .
                          "<a onclick=\"Clic('/admin/delete_cron', 'cronid=" . $array['id'] . "&crondesc=" . $array['desc'] . "', 'server_infoaction'); return false;\">" .
                          "<img width='24' src='" . ICO_PATH . "32x32/delete.png'/></a></td>";
            $tab_crons .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_crons .= '<tr class="pied"><td colspan="2" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->msg('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->msg('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/admin/display_server\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_previous') . '</a>';
        }
        $tab_crons .= '<td colspan="4" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_crons .= '<a onclick="Clic(\'/admin/display_server\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_crons .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->msg('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/admin/display_server\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_next') . '</a>';
        }
        
        $tab_crons .= $next . '</td></tr></table>' .
                      "<div class='bouton' style='width:120px;'><a onclick=\"Clic('/admin/display_createcron', '', 'milieu_milieu'); return false;\">" .
                      "<span style='color: #dfdfdf;'>" . $translate->msg('btn_addcron') . "</span></a></div>";
        
        return $tab_crons;
    }
    
    
    // Mise en page tabulaire de la liste des logs
    // ===========================================
    protected function _renderLogs()
    {
        $paginator = new plugins_paginator(admin_logs::display_dblogs());
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = memory_registry::get("translate");
        
        $tab_logs = '<table id="pagination_motions" class="pagination"><tr class="entete">' .
                    '<td width="20px">' . $translate->msg("logs_level") . '</td>' .
                    '<td width="150px">' . $translate->msg("logs_date") . '</td>' .
                    '<td>' . $translate->msg("logs_description") . '</td>' .
                    '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_logs .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "Motion_id") {
                    $tab_logs .= "<td>" . $this->_filterLogs($key, $value) . "</td>";
                }
            }
            $tab_logs .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_logs .= '<tr class="pied"><td colspan="2" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->msg('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->msg('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_previous') . '</a>';
        }
        $tab_logs .= '<td style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_logs .= '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_logs .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->msg('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/admin/display_logs\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_next') . '</a>';
        }
        
        $tab_logs .= $next . '</td></tr></table>';
        
        return $tab_logs;
    }
        
    
    // Mise en page tabulaire de la liste des logs
    // ===========================================
    protected function _renderUsers()
    {
        $paginator = new plugins_paginator(members::ListMembers());
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = memory_registry::get("translate");
        
        $tab_users = '<table id="pagination_users" class="pagination"><tr class="entete">' .
                    '<td width="50px">' . $translate->msg("avatar") . '</td>' .
                    '<td width="200px">' . $translate->msg("identity") . '</td>' .
                    '<td width="50px">' . $translate->msg("gender") . '</td>' .
                    '<td width="200px">' . $translate->msg("mail") . '</td>' .
                    '<td width="50px">' . $translate->msg("lang") . '</td>' .
                    '<td width="150px">' . $translate->msg("country") . '</td>' .
                    '<td width="150px">' . $translate->msg("region") . '</td>' .
                    '<td width="150px">' . $translate->msg("last_connexion") . '</td>' .
                    '<td width="150px">' . $translate->msg("subscription") . '</td>' .
                    '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_users .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "iduser") {
                    $tab_users .= "<td>" . $this->_filterUsers($key, $value) . "</td>";
                }
            }
            $tab_users .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_users .= '<tr class="pied"><td colspan="6" align="left">' . $nFirstItem . '-' . $nLastItem . $translate->msg('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->msg('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/admin/display_users\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_previous') . '</a>';
        }
        $tab_users .= '<td style="padding-right:15px;" align="right" colspan="3">' . $previous . ' | ';
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_users .= '<a onclick="Clic(\'/admin/display_users\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_users .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->msg('page_next') . '</span>';
        
        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/admin/display_users\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_next') . '</a>';
        }
        
        $tab_users .= $next . '</td></tr></table>';
        
        return $tab_users;
    }
        
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterLogs($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "label_key":
                $translate = memory_registry::get("translate");
                $value = $translate->msg($value);
                break;
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
                $db = memory_registry::get('db');
                $member = members::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT " . $lang . " FROM country WHERE code = '" . $value . "' LIMIT 1");
                if (count($res) >0)    {   $value = $res[0][$lang];                }
                else                
                {
                    $translate = translate::getInstance();
                    $value = $translate->msg("unknown");
                }
                break;
            
            case "region":
                $db = memory_registry::get('db');
                $member = members::getInstance();
                $lang = $member->langue;
                $res = $db->select("SELECT Name FROM regions WHERE Region_id = " . $value . " LIMIT 1");
                if (count($res) >0 && $value >0)    {   $value = utf8_encode($res[0]['Name']);        }
                else                
                {
                    $translate = translate::getInstance();
                    $value = $translate->msg("unknown");
                }
                break;
        }
        
        return $value;
    }
                
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterCrons($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "desc":
                $value = utf8_encode($value);
                break;
                
            case "enabled":
                if ($value == 1)        {   $value = '<img width="16" alt="active" src="' . ICO_PATH . '32x32/valid.png">';        }
                else                    {   $value = '<img width="16" alt="inactive" src="' . ICO_PATH . '32x32/echec.png">';      }
                break;
        }
        
        return $value;
    }
}
?>
