<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Admin\Log;

use Wonderland\Library\Http\Response\Response;
use Wonderland\Library\Http\Response\JsonResponse;

class MotionController extends ActionController {
    public function newAction() {
        $this->checkAccess('citizenship');
        
        return $this->render('motions/new', [
            'themes' => $this->application->get('motion_manager')->getMotionThemes(),
            'identity' => $this->getUser()->getIdentity(),
            'avatar' => $this->getUser()->getAvatar(),
            'nb_unread_messages' => $this->application->get('message_manager')->countUnreadMessages($this->getUser()->getId())
        ]);
    }
    
    public function createAction() {
        $this->checkAccess('citizenship');
        
        $motion = $this->application->get('motion_manager')->createMotion(
            $this->request->get('title'),
            $this->request->get('description'),
            $this->request->get('theme'),
            $this->getUser(),
            $this->request->get('means')
        );
        return new JsonResponse($motion, 201);
    }
    
    public function displayCreateMotionAction() {
        $translate = $this->application->get('translator');
        $motionThemes = $this->application->get('motion_manager')->getMotionThemes();
        
        $selectTheme = '<option></options>';
        
        while ($theme = $motionThemes->fetch(\PDO::FETCH_ASSOC)) {
            $selectTheme .= "<option value='{$theme['theme_id']}'>{$translate->translate($theme['label_key'])}</option>";
        }
        return $this->render('actions/create_motion', [
            'msg' => '',
            'select_theme' => $selectTheme
        ]);
    }
    
    public function displayMotionsInProgressAction() {
        return $this->render('actions/motions_inprogress', [
            'list_motions' => $this->application->get('motion_manager')->displayActiveMotions($this->getUser())
        ]);
    }
    
    public function displayMotionsAction() {
        return $this->render('actions/motions', [
            'list_motions' => $this->renderMotions()
        ]);
    }
    
    public function displayVoteAction() {
        $details = $this->application->get('motion_manager')->displayMotionDetails($_POST['motion_id']);
        
        return $this->render('actions/motion_vote_details', [
            'details' => $details[0],
            'description' => str_replace('&gt;', '>', str_replace('&lt;', '<', $details[0]['description'])),
            'means' => html_entity_decode($details[0]['moyens'])
        ]);
    }
    
    public function displayMotionAction() {
        $details = $this->application->get('motion_manager')->displayMotionDetails($_POST['motion_id']);
        
        return $this->render('actions/motion_details', [
            'details' => $details[0],
            'description' => str_replace('&gt;', '>', str_replace('&lt;', '<', $details[0]['description'])),
            'means' => html_entity_decode($details[0]['moyens'])
        ]);
    }
    
    /**
     * @return string
     */
    protected function renderMotions() {
        $motionManager = $this->application->get('motion_manager');
        $paginator = $this->application->get('paginator');
        $paginator->setData($motionManager->displayMotions());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $motions = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translator = $this->application->get('translator');
        
        $tab_motions =
            '<table id="pagination_motions" class="pagination"><tr class="entete">' .
            '<td>' . $translator->translate('title_motion') . '</td>' .
            '<td>' . $translator->translate('theme_motion') . '</td>' .
            '<td>' . $translator->translate('date_deposit') . '</td>' .
            '<td>' . $translator->translate('date_end_vote') . '</td>' .
            '<td>' . $translator->translate('author_motion') . '</td>' .
            '<td>' . $translator->translate('status_motion') . '</td></tr>'
        ;
        
        foreach($motions as $motion) {
            $tab_motions .=
                "<tr style=\"height:25px\" onclick=\"Clic('Motion/displayMotion', " .
                "'motion_id={$motion['Motion_id']}', 'milieu_milieu'); return false;\">" .
                "<td>{$motion['Title_key']}</td>" .
                "<td>{$translator->translate($motion['Label_key'])}</td>" .
                "<td>{$motion['Submission_date']}</td>" .
                "<td>{$motion['Date_fin_vote']}</td>" .
                "<td>{$motion['identity']}</td>"
            ;
            $votes = $motionManager->getVotes($motion['Motion_id']);
            
            if ($votes[0] > $votes[1]) {
                $percent = round((($votes[0] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote =
                    "<div class='motion_approved' title='{$votes[0]} {$translator->translate('vote_approve')} " .
                    "- {$votes[1]} {$translator->translate('vote_refuse')}'>$percent%</div>"
                ;
            } else {
                $percent = round((($votes[1] / ($votes[0] + $votes[1]) * 100) * 100) / 100, 2);
                $resultat_vote =
                    "<div class='motion_refused' title='{$votes[0]} {$translator->translate('vote_approve')} " .
                    "- {$votes[1]} {$translator->translate('vote_refuse')}'>$percent%</div>"
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
        $tab_motions .= '<tr class="pied"><td align="left">' . $nFirstItem . '-' . $nLastItem . $translator->translate('item_of') . $paginator->countItems() . '</td>';
        
        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">' . $translator->translate('page_previous') . '</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Motion/displayMotions\', \'&page=' . ($CurPage-1) . '\', \'milieu_milieu\'); return false;">' . $translator->translate('page_previous') . '</a>';
        }
        $tab_motions .= '<td colspan="5" style="padding-right:15px;" align="right">' . $previous . ' | ';
        $start = $CurPage - $paginator->getPageRange();
        $end = $CurPage + $paginator->getPageRange();
        if ($start<1)   {   $start =1;  }
        if ($end > $MaxPage) {   $end = $MaxPage;     }
        
        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_motions .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'Motion/displayMotions\', \'&page=' . $page . '\', \'milieu_milieu\'); return false;">' . $page . '</a> | '
                : '<b>' . $page . '</b> | '
            ;
        }
        $next = '<span class="disabled">' . $translator->translate('page_next') . '</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Motion/displayMotions\', \'&page=' . ($CurPage+1) . '\', \'milieu_milieu\'); return false;">' . $translator->translate('page_next') . '</a>';
        }
        return $tab_motions . $next . '</td></tr></table>';
    }
    
    public function createMotionAction() {
        $translate = $this->application->get('translator');
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $motionManager = $this->application->get('motion_manager');
        $motionThemes = $motionManager->getMotionThemes();
        $selectTheme = '<option></options>';
        while ($theme = $motionThemes->fetch(\PDO::FETCH_ASSOC)) {
            $selectTheme .= "<option value='{$theme['theme_id']}'>{$translate->translate($theme['label_key'])}</option>";
        }
        
        if(
            !empty($_POST['theme']) && !empty($_POST['title_motion']) &&
            !empty($_POST['description_motion'])&& !empty($_POST['means_motion'])
        ) {
            if ($motionManager->validateMotion($this->getUser(), $_POST['title_motion'], $_POST['theme'], $_POST['description_motion'], $_POST['means_motion'])) {
                $msg =
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('depot_motion_ok') . '</span></td>' .
                    '</tr></table></div>'
                ;
            } else {
                $msg = 
                    '<div class="error" style="height:50px;"><table><tr>' .
                    '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('depot_motion_nok') . '</span></td>' .
                    '</tr></table></div>'
                ;
                // Journal de log
                $logger->log("Echec du dépot de motion par l'utilisateur {$this->getUser()->getIdentity()}", Log::WARN);
            }
        } else {
            $msg = 
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('fields_empty') . '</span></td>' .
                '</tr></table></div>'
            ;
            $logger->log("Echec du dépot de motion par l'utilisateur {$this->getUser()->getIdentity()} (champs vides)", Log::WARN);
        }
        return $this->render('actions/create_motion', [
            'msg' => $msg
        ]);
    }
    
    public function voteMotionAction() {
        $translate = $this->application->get('translator');
        if (!empty($_POST['motion_id']) && !empty($_POST['vote'])) {
            if ($this->application->get('motion_manager')->voteMotion($this->getUser(), $_POST['motion_id'], $_POST['vote']) === 1) {
                return new Response(
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('vote_motion_ok') . '</span></td>' .
                    '</tr></table></div>' .
                    '<script type="text/javascript">Clic("/Motion/displayMotionsInProgress", "", "milieu_gauche");</script>'
                );
            }
            // Journal de log
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec du vote de motion par l'utilisateur {$this->getUser()->getIdentity()}", Log::WARN);
        
            return new Response(
                '<div class="error" style="height:50px;"><table><tr>' .
                '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('vote_motion_nok') . '</span></td>' .
                '</tr></table></div>'
            );
        }
    }
    
    public function checkMotionAction() {
        $this->application->get('motion_manager')->checkMotion();
    }
}