<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Application\Model\Poll;
use Wonderland\Application\Model\Member;

use Wonderland\Library\Admin\Log;

class MotionController extends ActionController {
    public function displayCreateMotionAction() {
        $translate = $this->application->get('translate');
        $list_themes = Poll::_getThemes();
        
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['msg'] = '';
        $this->viewParameters['select_theme'] = '<option></options>';
        
        for ($i = 0; ($theme = $list_themes->fetch_assoc()); ++$i) {
            $this->viewParameters['select_theme'] .= "<option value='$i'>{$translate->translate($theme['label_key'])}</option>";
        }
        $this->render('actions/create_motion');
    }
    
    public function displayMotionsInProgressAction() {
        $this->viewParameters['list_motions'] = $this->application->get('motion_manager')->displayActiveMotions();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('actions/motions_inprogress');
    }
    
    public function displayMotionsAction() {
        $this->viewParameters['list_motions'] = $this->renderMotions();
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->render('actions/motions');
    }
    
    public function displayVoteAction() {
        $polls = new Poll;
        $details = $polls->displayMotionDetails($_POST['motion_id']);
        
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->viewParameters['details'] = $details[0];
        $this->viewParameters['description'] = str_replace('&gt;', '>', str_replace('&lt;', '<', $details[0]['description']));
        $this->viewParameters['means'] = html_entity_decode($details[0]['moyens']);
        $this->render('actions/motion_vote_details');
    }
    
    public function displayMotionAction() {
        $polls = new Poll;
        $details = $polls->displayMotionDetails($_POST['motion_id']);
        
        $this->viewParameters['translate'] = $this->application->get('translate');
        $this->viewParameters['details'] = $details[0];
        $this->viewParameters['description'] = str_replace('&gt;', '>', str_replace('&lt;', '<', $details[0]['description']));
        $this->viewParameters['means'] = html_entity_decode($details[0]['moyens']);
        $this->render('actions/motion_details');
    }
    
    /**
     * @return string
     */
    protected function renderMotions() {
        $polls = new Poll;
        $paginator = $this->application->get('paginator');
        $paginator->setData($polls->displayMotions());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translate');
        
        $tab_motions =
            '<table id="pagination_motions" class="pagination"><tr class="entete">' .
            '<td>' . $translate->translate('title_motion') . '</td>' .
            '<td>' . $translate->translate('theme_motion') . '</td>' .
            '<td>' . $translate->translate('date_deposit') . '</td>' .
            '<td>' . $translate->translate('date_end_vote') . '</td>' .
            '<td>' . $translate->translate('author_motion') . '</td>' .
            '<td>' . $translate->translate('status_motion') . '</td></tr>'
        ;
        
        foreach($datas as $key => $row) {
            $tab_motions .= '<tr style="height:25px">';
            foreach($row as $key => $value) {
                if ($key !== 'Motion_id') {
                    $tab_motions .=
                        "<td><a style='background-color:#000000' onclick=\"Clic('/motions/display_motion', " .
                        "'motion_id={$row['Motion_id']}', 'milieu_milieu'); return false;\">{$this->filterMotions($key, $value)}</a></td>"
                    ;
                }
            }
            $votes = Poll::_getVotes($row['Motion_id']);
            if ($votes[0] > $votes[1]) {
                $percent = round((($votes[0] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote =
                    "<div class='motion_approved' title='{$votes[0]} {$translate->translate('vote_approve')} " .
                    "- {$votes[1]} {$translate->translate('vote_refuse')}'>$percent%</div>"
                ;
            } else {
                $percent = round((($votes[1] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote =
                    "<div class='motion_refused' title='{$votes[0]} {$translate->translate('vote_approve')} " .
                    "- {$votes[1]} {$translate->translate('vote_refuse')}'>$percent%</div>"
                ;
            }
            $tab_motions .= "<td align='center'>$resultat_vote</td></tr>";
        }
        
        // numéros des items
        $nFirstItem = (($CurPage - 1) * $paginator->getItemsPerPage())+1;
        $nLastItem = ($CurPage * $paginator->getItemsPerPage());
        
        if ($nLastItem>$paginator->countItems()) {
            $nLastItem = $paginator->countItems();
        }
        $tab_motions .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translate->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translate->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_previous') . '</a>';
        }
        $tab_motions .= '<td colspan="5" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_motions .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translate->translate('page_next') . '</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'/motions/display_motions\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translate->translate('page_next') . '</a>';
        }
        return $tab_motions . $next . '</td></tr></table>';
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function filterMotions($key, $value) {
        switch(strtolower($key)) {
            case 'citizen_id':
                $identite = $this->application->get('mysqli')->select("SELECT Identite FROM Utilisateurs WHERE IDUser=$value");
                return
                    (isset($identite[0]['Identite']))
                    ? $identite[0]['Identite']
                    : $this->application->get('translate')->translate('unknown')
                ;
            
            case 'label_key':
                return $this->application->get('translate')->translate($value);
            
            case 'submission_date':
                return explode(' ', $value)[0];
                        
            case 'date_fin_vote':
                return explode(' ', $value)[0];
        }
    }
    
    public function createMotionAction() {
        $translate = $this->application->get('translate');
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $list_themes = Poll::_getThemes();
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['select_theme'] = '<option></options>';
        for ($i = 0; ($theme = $list_themes->fetch_assoc()); ++$i) {
            $this->viewParameters['select_theme'] .= "<option value='$i'>{$translate->translate($theme['label_key'])}</option>";
        }
        
        if(
            !empty($_POST['theme']) && !empty($_POST['title_motion']) &&
            !empty($_POST['description_motion'])&& !empty($_POST['means_motion'])
        ) {
            $polls = new Poll;
            if ($polls->valid_motion($_POST['title_motion'], $_POST['theme'], $_POST['description_motion'], $_POST['means_motion'])) {
                $this->viewParameters['msg'] =
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('depot_motion_ok') . '</span></td>' .
                    '</tr></table></div>'
                ;
            } else {
                $this->viewParameters['msg'] = 
                    '<div class="error" style="height:50px;"><table><tr>' .
                    '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('depot_motion_nok') . '</span></td>' .
                    '</tr></table></div>'
                ;
                
                // Journal de log
                $member = Member::getInstance();
                $logger->log("Echec du dépot de motion par l'utilisateur {$member->identite}", Log::WARN);
            }
        } else {
            $this->viewParameters['msg'] = 
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('fields_empty') . '</span></td>' .
                '</tr></table></div>'
            ;
            $member = Member::getInstance();
            $logger->log("Echec du dépot de motion par l'utilisateur {$member->identite} (champs vides)", Log::WARN);
        }
        $this->render('actions/create_motion');
    }
    
    public function voteMotionAction() {
        $translate = $this->application->get('translate');
        $this->viewParameters['translate'] = $translate;
        if (!empty($_POST['motion_id']) && !empty($_POST['vote'])) {
            $polls = new Poll();
            if ($polls->vote_motion($_POST['motion_id'], $_POST['vote']) === 1) {
                $this->display(
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('vote_motion_ok') . '</span></td>' .
                    '</tr></table></div>' .
                    '<script type="text/javascript">Clic("/motions/display_motionsinprogress", "", "milieu_gauche");</script>'
                );
            } else {
                $this->display(
                    '<div class="error" style="height:50px;"><table><tr>' .
                    '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('vote_motion_nok') . '</span></td>' .
                    '</tr></table></div>'
                );
                // Journal de log
                $member = Member::getInstance();
                $logger = $this->application->get('logger');
                $logger->setWriter('db');
                $logger->log("Echec du vote de motion par l'utilisateur {$member->identite}", Log::WARN);
            }
        }
    }
    
    public function checkMotionAction() {
        (new Poll())->checkMotion();
    }
}