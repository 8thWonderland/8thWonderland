<?php

/**
 * Controleur des motions
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/


class motions extends controllers_action {

    public function display_createmotionAction()
    {
        $translate = memory_registry::get("translate");
        $list_themes = polls::_getThemes();
        
        $this->_view['translate'] = $translate;
        $this->_view['msg'] = '';
        $this->_view['select_theme'] = "<option></options>";
        
        $i=0;
        while ($theme = $list_themes->fetch_assoc()) {
            $i++;
            $this->_view['select_theme'] .= "<option value='" . $i . "'>" . $translate->msg($theme['label_key']) . "</option>";
        }
        
        $this->render('actions/create_motion');
    }
    
    
    public function display_motionsinprogressAction()
    {
        $polls = new polls;
        $this->_view['list_motions'] = $polls->display_motionsinprogress();
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render("actions/motions_inprogress");
    }
    
    
    public function display_motionsAction()
    {
        $this->_view['list_motions'] = $this->_renderMotions();
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('actions/motions');
    }
    
    
    public function display_voteAction()
    {
        $polls = new polls;
        $details = $polls->display_detailsmotion($_POST['motion_id']);
        
        $this->_view['translate'] = memory_registry::get("translate");
        $this->_view['details'] = $details[0];
        $this->_view['description'] = str_replace("&gt;", ">", str_replace("&lt;", "<", $details[0]['description']));
        $this->_view['means'] = html_entity_decode($details[0]['moyens']);
        $this->render('actions/motion_vote_details');
    }
    
    
     public function display_motionAction()
    {
        $polls = new polls;
        $details = $polls->display_detailsmotion($_POST['motion_id']);
        
        $this->_view['translate'] = memory_registry::get("translate");
        $this->_view['details'] = $details[0];
        $this->_view['description'] = str_replace("&gt;", ">", str_replace("&lt;", "<", $details[0]['description']));
        $this->_view['means'] = html_entity_decode($details[0]['moyens']);
        $this->render('actions/motion_details');
    }
    
    // Mise en page tabulaire de la liste des motions à afficher
    // =========================================================
    protected function _renderMotions()
    {
        $polls = new polls;
        $paginator = new plugins_paginator($polls->display_motions());
        $paginator->_setCurrentPage(1);
        if (isset($_POST['page']) && !empty($_POST['page']))        {   $paginator->_setCurrentPage($_POST['page']);  }
        $datas = $paginator->_getCurrentItems();
        $CurPage = $paginator->_getCurrentPage();
        $MaxPage = $paginator->_getNumPage();
        $translate = memory_registry::get("translate");
        
        $tab_motions = '<table id="pagination_motions" class="pagination"><tr class="entete">' .
                       '<td>' . $translate->msg("title_motion") . '</td>' .
                       '<td>' . $translate->msg("theme_motion") . '</td>' .
                       '<td>' . $translate->msg("date_deposit") . '</td>' .
                       '<td>' . $translate->msg("date_end_vote") . '</td>' .
                       '<td>' . $translate->msg("author_motion") . '</td>' .
                       '<td>' . $translate->msg("status_motion") . '</td>' .
                       '</tr>';
        
        foreach($datas as $key => $row) {
            $tab_motions .= "<tr style='height:25px'>";
            foreach($row as $key => $value) {
                if ($key != "Motion_id") {
                    $tab_motions .= "<td><a style='background-color:#000000' onclick=\"Clic('/motions/display_motion', 'motion_id=" . $row['Motion_id'] . "', 'milieu_milieu'); return false;\">" . $this->_filterMotions($key, $value) . "</a></td>";
                }
            }
            $votes = polls::_getVotes($row['Motion_id']);
            $resultat_vote = '';
            if ($votes[0] > $votes[1])
            {
                $percent = round((($votes[0] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote = "<div class='motion_approved' title='".$votes[0]." ".$translate->msg('vote_approve')." - ".$votes[1]." ".$translate->msg('vote_refuse')."'>" . $percent . "%</div>";
            }
            else
            {
                $percent = round((($votes[1] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote = "<div class='motion_refused' title='".$votes[0]." ".$translate->msg('vote_approve')." - ".$votes[1]." ".$translate->msg('vote_refuse')."'>" . $percent . "%</div>";
            }
            $tab_motions .= "<td align='center'>" . $resultat_vote . "</td>";
            $tab_motions .= "</tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->_getItemsPage())+1;
        $nLastItem = ($CurPage * $paginator->_getItemsPage());
        if ($nLastItem>$paginator->_getItems())     {   $nLastItem = $paginator->_getItems();   }
        $tab_motions .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->msg('item_of') . $paginator->_getItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->msg('page_previous') . '</span>';
        if ($CurPage > 1)
        {
            $previous = '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_previous') . '</a>';
        }
        $tab_motions .= '<td colspan="5" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->_getPageRange();
        $end = $CurPage + $paginator->_getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page=$start; $page<$end+1; $page++) {
            if ($page != $CurPage)
            {
                $tab_motions .= '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | ';
            }
            else
            {
                $tab_motions .= '<b>' . $page . '</b> | ';
            }
        }
        $next = '<span class="disabled">' . $translate->msg('page_next') . '</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage)
        {
            $next = '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->msg('page_next') . '</a>';
        }
        
        $tab_motions .= $next . '</td></tr></table>';
        
        return $tab_motions;
    }
    
    
    // Affichage du filtre de données si il existe
    // ===========================================
    protected function _filterMotions($key, $value)
    {
        $key = strtolower($key);
        switch($key) {
            case "citizen_id":
                $translate = memory_registry::get("translate");
                $db = memory_registry::get('db');
                $identite = $db->select("SELECT Identite FROM Utilisateurs WHERE IDUser=" . $value);
                $value = (isset($identite[0]['Identite'])?$identite[0]['Identite']:$translate->msg("unknown"));
                break;
            
            case "label_key":
                $translate = memory_registry::get("translate");
                $value = $translate->msg($value);
                break;
            
            case "submission_date":
                $value = explode(" ", $value);
                $value = $value[0];
                        
            case "date_fin_vote":
                $value = explode(" ", $value);
                $value = $value[0];
        }
        
        return $value;
    }
    
    
    // Controle et enregistrement de la motion
    // =======================================
    public function create_motionAction()
    {
        $translate = memory_registry::get("translate");
        $list_themes = polls::_getThemes();
        $this->_view['translate'] = $translate;
        $this->_view['select_theme'] = "<option></options>";
        $i=0;
        while ($theme = $list_themes->fetch_assoc()) {
            $i++;
            $this->_view['select_theme'] .= "<option value='" . $i . "'>" . $translate->msg($theme['label_key']) . "</option>";
        }
        
        if(!empty($_POST['theme']) && !empty($_POST['title_motion'])&& !empty($_POST['description_motion'])&& !empty($_POST['means_motion'])) {
            $polls = new polls;
            if ($polls->valid_motion($_POST['title_motion'], $_POST['theme'], $_POST['description_motion'], $_POST['means_motion']))
            {
                $this->_view['msg'] = '<div class="info" style="height:50px;"><table><tr>' .
                                      '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                                      '<td><span style="font-size: 15px;">' . $translate->msg('depot_motion_ok') . '</span></td>' .
                                      '</tr></table></div>';
            }
            else
            {
                $this->_view['msg'] = '<div class="error" style="height:50px;"><table><tr>' .
                                      '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                                      '<td><span style="font-size: 15px;">' . $translate->msg('depot_motion_nok') . '</span></td>' .
                                      '</tr></table></div>';
                
                // Journal de log
                $member = members::getInstance();
                $db_log = new Log("db");
                $db_log->log("Echec du dépot de motion par l'utilisateur " . $member->identite, Log::WARN);
            }
        }
        else
        {
            $this->_view['msg'] = '<div class="error" style="height:50px;"><table><tr>' .
                                  '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                                  '<td><span style="font-size: 15px;">' . $translate->msg('fields_empty') . '</span></td>' .
                                  '</tr></table></div>';
            
            // Journal de log
            $member = members::getInstance();
            $db_log = new Log("db");
            $db_log->log("Echec du dépot de motion par l'utilisateur " . $member->identite . " (champs vides)", Log::WARN);
        }
        
        $this->render('actions/create_motion');
    }
    
    
    public function vote_motionAction()
    {
        $translate = memory_registry::get("translate");
        $this->_view['translate'] = $translate;
        if (!empty($_POST['motion_id']) && !empty($_POST['vote']))
        {
            $polls = new polls();
            if ($polls->vote_motion($_POST['motion_id'], $_POST['vote']) == 1)
            {
                $this->display('<div class="info" style="height:50px;"><table><tr>' .
                               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->msg('vote_motion_ok') . '</span></td>' .
                               '</tr></table></div>' .
                               '<script type="text/javascript">Clic("/motions/display_motionsinprogress", "", "milieu_gauche");</script>');
            }
            else
            {
                $this->display('<div class="error" style="height:50px;"><table><tr>' .
                               '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->msg('vote_motion_nok') . '</span></td>' .
                               '</tr></table></div>');
                
                // Journal de log
                $member = members::getInstance();
                $db_log = new Log("db");
                $db_log->log("Echec du vote de motion par l'utilisateur " . $member->identite, Log::WARN);
            }
        }
        
    }
    
    
     public function check_motionAction()
    {
        $polls = new polls();
        $polls->check_motion();
    }
}
?>
