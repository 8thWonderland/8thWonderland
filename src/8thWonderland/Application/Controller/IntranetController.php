<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Mailer;

use Wonderland\Library\Admin\Log;

class IntranetController extends ActionController {
    public function indexAction() {
        if (($member = $this->getUser()) === null) {
            $this->redirect('Index/index');
        }
        $this->viewParameters['translate'] = $this->application->get('translator');
        
        $select_geo = false;
        $db = $this->application->get('database_connection');

        // Teste si le code country du membre est valide
        // =============================================
        $country_ok = $db->query(
            "SELECT COUNT(*) AS count FROM country WHERE code='{$member->getCountry()}'"
        )->fetch()['count'];
        if ($country_ok === 0) {
            $select_geo = true;
        } else {
            $region_member = $member->getRegion();
            if (!isset($region_member) || $region_member === -2) {
                $select_geo = true;
            }
        }

        if ($select_geo) {
            $this->displaySelectCountry($member);
        } else {
            $this->displayIntranet($member);
        }
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    protected function displaySelectCountry(Member $member) {
        $language = $member->getLanguage();
        $statement = $this->application->get('member_manager')->getCountries($language);
        $this->viewParameters['select_country'] = '<option></option>';
        while($country = $statement->fetch()) {
            $this->viewParameters['select_country'] .= "<option value='{$country['Code']}'>{$country[$language]}</option>";
        }
        $this->viewParameters['msg'] = '';
        $this->viewParameters['default_view'] = 'members/select_country.view';
        $this->render('connected');
    }
    
    protected function displayIntranet(Member $member) {
        $session = $this->application->get('session');
        if (!empty($_POST['group_id'])) {
            $session->set('desktop', $_POST['group_id']);
        }
        $memberManager = $this->application->get('member_manager');
        // affichage du profil
        $this->viewParameters['identity'] = $member->getIdentity();
        $this->viewParameters['avatar'] = $member->getAvatar();
        $this->viewParameters['admin'] = $memberManager->isMemberInGroup($member, 1);

        $desktop = $session->get('desktop');
        if (isset($desktop)) {
            $this->viewParameters['Contact_Group'] = $memberManager->isContact($member, $desktop);
            $this->viewParameters['haut_milieu'] =
                ($desktop === 1)
                ? VIEWS_PATH . 'admin/menu_admin.view'
                : VIEWS_PATH . 'groups/menu_groups.view'
            ;

            $this->viewParameters['milieu_droite'] = 
                "<table>" .
                "<tr><td id='md_section1'><script type='text/javascript'>window.onload=Clic('Admin/displayStatsCountry', '', 'md_section1');</script></td></tr>" .
                "<tr><td id='md_section2'><script type='text/javascript'>window.onload=Clic('Group/displayMembers', '', 'md_section2');</script></td></tr>" .
                "</table>"
            ;
            $this->viewParameters['milieu_milieu'] = "";
            $this->viewParameters['milieu_gauche'] = "<script type='text/javascript'>window.onload=Clic('Member/displayContactsGroups', '', 'milieu_gauche');</script>";
        } else {
            $this->viewParameters['haut_milieu'] = VIEWS_PATH . 'members/menu.view';
            $this->viewParameters['milieu_droite'] = '';
            $this->viewParameters['milieu_milieu'] = "<script type='text/javascript'>window.onload=Clic('Intranet/communicate', '', 'milieu_milieu');</script>";
            $this->viewParameters['milieu_gauche'] = "<script type='text/javascript'>window.onload=Clic('Motion/displayMotionsInProgress', '', 'milieu_gauche');</script>";
            $this->viewParameters['list_motions'] = $this->application->get('motion_manager')->displayActiveMotions($member);

            // affichage des groupes du membre
            $this->viewParameters['list_groups'] = $member->getGroups();
            $this->viewParameters['milieu_droite'] = "<script type='text/javascript'>window.onload=Clic('Group/displayGroupsMembers', '', 'milieu_droite');</script>";

        }
        if ($this->is_Ajax()) {
            $this->render('members/intranet');
        } else {
            $this->viewParameters['default_view'] = 'members/intranet.view';
            $this->render('connected');
        }
    }
    
    public function zoneGeoAction() {
        if (($member = $this->getUser()) === null) {
            $this->redirect('Index/index');
        }
        
        if (!empty($_POST['country']) && isset($_POST['region']) && $_POST['region'] !== 0) {
            $db = $this->application->get('database_connection');
            $db->prepareStatement(
                'UPDATE users SET country = :country, region = :region WHERE id = :id'
            , [
                'country' => $_POST['country'],
                'region' => $_POST['region'],
                'id' => $member->getId()
            ]);
            
            if ($_POST['region'] !== -1) {
                $logger = $this->application->get('logger');
                $logger->setWriter('db');
                // Ajout de l'utilisateur dans le groupe correspondant
                // ===================================================
                $echec_createGroup = false;
                $groupId = 0;
                $groupName = $db->prepareStatement(
                    'SELECT Name FROM regions WHERE Region_id = :region_id'
                , ['region_id' => $_POST['region']])->fetch()['Name'];
                if ($db->prepareStatement('SELECT COUNT(*) AS count FROM groups WHERE name = :name', ['name' => $groupName])->fetch()['count'] === 0) {
                    $statement = $db->prepareStatement(
                        'INSERT INTO groups (type_id, description, name, contact_id) VALUES (:type_id, :description, :name, :contact_id)', [
                            'type_id' => 1,
                            'description' => 'Groupe regional',
                            'name' => $groupName,
                            'contact_id' => 1
                        ]);
                    if ($statement->rowCount() === 0) {
                        $echec_createGroup = true;
                    } else {
                        // Journal de log
                        $logger->log("Création du groupe régional $groupName par l'utilisateur " . $member->getIdentity(), Log::ERR);
                        $groupId = $db->lastInsertId();
                    }
                } else {
                    $groupId = $db->query("SELECT id FROM groups WHERE name = '$groupName'")->fetch()['id'];
                }

                if (!$echec_createGroup) {
                    $statement = $db->prepareStatement(
                        'INSERT INTO citizen_groups (citizen_id, group_id) VALUES (:citizen_id, :group_id)'
                    , ['citizen_id' => $member->getId(), 'group_id' => $groupId]);
                    if ($statement->rowCount() === 1) {
                        // Journal de log
                        $logger->log("Ajout de l'utilisateur {$member->getIdentity()} dans le groupe {$groupName}", Log::ERR);
                    }
                }
            
            } else {
                // Si la region choisie est 'other' alors Brennan Waco reçoit un mail
                // ==================================================================
                $mail = new Mailer();
                $mail->addRecipient('kern046@gmail.com','');
                $mail->addFrom('developpeurs@8thwonderland.com','');
                $mail->addSubject('regions inconnues','');
                $mail->html = 
                    "<table><tr><td>ID User : {$member->getIdentity()} :<br/>====================</td></tr>" .
                    "<tr><td>{$_POST['country']}<br/></td></tr>" .
                    '<tr><td>Message :<br/>====================</td></tr></table>'
                ;
                $mail->send();
            }
            $this->redirect('Intranet/index');
        } else {
            $translate = $this->application->get('translator');
            $this->viewParameters['translate'] = $translate;
            $this->viewParameters['msg'] = $translate->translate('fields_empty');
            $this->display(json_encode([
                'status' => 2,
                'reponse' => $translate->translate('fields_empty')
            ]));
        }
    }
    
    public function listRegionsAction() {
        $res = '<option></option>';
        if (!empty($_POST['country'])) {
            $statement = $this->application->get('database_connection')->prepareStatement(
                'SELECT Region_id, Name FROM regions WHERE Country = :country ORDER BY Name ASC'
            , ['country' => $_POST['country']]);
            $regions= '';
            while($region = $statement->fetch()) {
                $regions .= "<option value='{$region['Region_id']}'>" . htmlentities($region['Name']) . "</option>";
            }
            if(empty($regions)) {
                $regions .= "<option value='-1'>Other</option>";
            }
        }
        $this->display($res . $regions);
    }

    public function infosAction() {
        if (($id = $this->application->get('session')->get('__id__')) === null) {
            $this->redirect('Index/index');
        }
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('informations/public_news');
    }

    public function shareAction() {
        if (($id = $this->application->get('session')->get('__id__')) === null) {
            $this->redirect('Index/index');
        }
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('admin/dev_inprogress');
    }

    public function communicateAction() {
        if (($id = $this->application->get('session')->get('__id__')) === null) {
            $this->redirect('Index/index');
        }
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('informations/public_news');
    }

    public function financeAction() {
        if (($id = $this->application->get('session')->get('__id__')) === null) {
            $this->redirect('Index/index');
        }
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('admin/dev_inprogress');
    }
    
    public function consoleAction() {
        if (($member = $this->getUser()) === null) {
            $this->redirect('Index/index');
        }
        $memberManager = $this->application->get('member_manager');
        if (!$memberManager->isMemberInGroup($member->getId(), 1)) {
            $this->redirect('Intranet/index');
        }
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $logger->log("{$member->getIdentity()} entre dans la console d'administration.", Log::INFO);
        
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->redirect('Admin/displayConsole');
    }
}