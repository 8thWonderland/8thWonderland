<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Auth;

use Wonderland\Library\Memory\Registry;

use Wonderland\Application\Model\ManageGroups;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Poll;
use Wonderland\Application\Model\Mailer;

use Wonderland\Library\Admin\Log;

class IntranetController extends ActionController {

    public function indexAction()
    {
        // controle si l'utilisateur est connecté
        if (!Auth::hasIdentity())    {   $this->redirect("Index/index");      }

        $this->_view['translate'] = Registry::get("translate");
        
        $select_geo = false;
        $member = Member::getInstance();
        $db = Registry::get('db');

        // Teste si le code country du membre est valide
        // =============================================
        $country_ok = $db->count("country", " WHERE code='" . $member->pays . "'");
        if ($country_ok == 0)   {   $select_geo = true;     }
        else {
            $region_member = $member->getRegion();
            if (!isset($region_member) || $region_member == -2)     {   $select_geo = true;     }
        }

        if ($select_geo)    {       $this->display_selectCountry();      }
        else                {       $this->display_intranet(); }
    }
    
    
    protected function display_selectCountry()
    {
        $member = Member::getInstance();
        $list_country = $member->listCountries();
        $this->_view['select_country'] = "<option></option>";
        $i=0;
        for ($i=0; $i<count($list_country); $i++) {
            $this->_view['select_country'] .= "<option value='" . $list_country[$i]['Code'] . "'>" . $list_country[$i][$member->langue] . "</option>";
        }

        $this->_view['msg'] = '';
        $this->_view['default_view'] = "members/select_country.view";
        $this->render("connected");
    }
    
    
    protected function display_intranet()
    {
        if (isset($_POST['group_id']) && !empty($_POST['group_id']))     {   Registry::set("desktop", $_POST['group_id']);    }
        
        // affichage du profil
        $member = Member::getInstance();
        $this->_view['identity'] = $member->identite;
        $this->_view['avatar'] = $member->avatar;
        $this->_view['admin'] = Member::EstMembre(1);

        
        $desktop = Registry::get("desktop");
        if (isset($desktop)) {
            $this->_view['Contact_Group'] = Member::isContact($desktop);
            if ($desktop == 1)  {   $this->_view['haut_milieu'] = VIEWS_PATH . "admin/menu_admin.view";     }
            else                {   $this->_view['haut_milieu'] = VIEWS_PATH . "groups/menu_groups.view";   }

            $milieu_droite = "<table>" .
                             "<tr><td id='md_section1'><script type='text/javascript'>window.onload=Clic('/admin/display_statsCountry', '', 'md_section1');</script></td></tr>" .
                             "<tr><td id='md_section2'><script type='text/javascript'>window.onload=Clic('/groups/display_members', '', 'md_section2');</script></td></tr>" .
                             "</table>";
            
            $this->_view['milieu_droite'] = $milieu_droite;
            $this->_view['milieu_milieu'] = "";
            $this->_view['milieu_gauche'] = "<script type='text/javascript'>window.onload=Clic('/member/display_contactsgroups', '', 'milieu_gauche');</script>";
            
            
            if ($this->is_Ajax())   {   $this->render("members/intranet");      }
            else
            {
                $this->_view['default_view'] = "members/intranet.view";
                $this->render("connected");
            }
        } else {
            // affichage des motions en cours
            $poll = new Poll;
            $this->_view['haut_milieu'] = VIEWS_PATH . "members/menu.view";
            $this->_view['milieu_droite'] = "";
            $this->_view['milieu_milieu'] = "<script type='text/javascript'>window.onload=Clic('/intranet/communicate', '', 'milieu_milieu');</script>";
            $this->_view['milieu_gauche'] = "<script type='text/javascript'>window.onload=Clic('/motions/display_motionsinprogress', '', 'milieu_gauche');</script>";
            $this->_view['list_motions'] = $poll->display_motionsinprogress();

            // affichage des groupes du membre
            $this->_view['list_groups'] = ManageGroups::display_groupsMember();
            $this->_view['milieu_droite'] = "<script type='text/javascript'>window.onload=Clic('/groups/display_groupsmembers', '', 'milieu_droite');</script>";


            if ($this->is_Ajax())   {   $this->render("members/intranet");      }
            else
            {
                $this->_view['default_view'] = "members/intranet.view";
                $this->render("connected");
            }
        }
    }
    
    
    // validation du pays et de la region
    // ==================================
    public function zone_geoAction()
    {
        // controle si l'utilisateur est connecté
        // ======================================
        if (!Auth::hasIdentity())       {   $this->redirect("Index/index");     }
        
        
        if (isset($_POST['country']) && !empty($_POST['country']) && isset($_POST['region']) && $_POST['region'] != 0)
        {
            $auth = Auth::getInstance();
            $db = Registry::get('db');
            $member = Member::getInstance();
            
            // Enregistrement du pays et de la region de l'utilisateur
            // =======================================================
            $req = "UPDATE Utilisateurs " .
                   "SET Pays='" . $_POST['country'] . "', Region=" . $_POST['region'] . " " .
                   "WHERE IDUser=" . $auth->_getIdentity();
            $db->query($req);
            
            
            if ($_POST['region'] != -1) {
                // Ajout de l'utilisateur dans le groupe correspondant
                // ===================================================
                $echec_createGroup = false;
                $id_group = 0;
                $group_name = $db->select("SELECT Name FROM regions WHERE Region_id=" . $_POST['region']);
                if ($db->count("Groups", " WHERE Group_name='" . $db->real_escape_string($group_name[0]['Name']) . "'") == 0) {
                    $db->query("INSERT INTO Groups (Group_Type, Description, Group_name, ID_Contact) VALUES (1, 'Groupe regional', '" . $db->real_escape_string($group_name[0]['Name']) . "', 5)");
                    if ($db->affected_rows == 0) {
                        $echec_createGroup = true;
                        // Journal de log
                        $db_log = new Log("db");
                        $db_log->log("Création du groupe régional " . $group_name[0]['Name'] . " par l'utilisateur " . $member->identite, Log::ERR);
                    } else {
                        $id_group = $db->insert_id;
                    }
                } else {
                    $group = $db->select("SELECT Group_id FROM Groups WHERE Group_name='" . $db->real_escape_string($group_name[0]['Name']) . "'");
                    $id_group = $group[0]['Group_id'];
                }

                if (!$echec_createGroup) {
                    $db->query("INSERT INTO Citizen_Groups (Citizen_id, Group_id) VALUES (" . $auth->_getIdentity() . ", " . $id_group . ")");
                    if ($db->affected_rows == 0) {
                        // Journal de log
                        $db_log = new Log("db");
                        $db_log->log("Ajout de l'utilisateur " . $member->identite . " dans le groupe " . $group_name[0]['Name'], Log::ERR);
                    }
                }
            
            } else {
                // Si la region choisie est 'other' alors Brennan Waco reçoit un mail
                // ==================================================================
                $mail = Mailer::getInstance();
                $mail -> addrecipient('waco.brennan@gmail.com','');
                $mail -> addfrom('developpeurs@8thwonderland.com','');
                $mail -> addsubject('regions inconnues','');
                $message = "<table>" .
                    "<tr><td>ID User : " . $auth->_getIdentity() . " :<br/>====================</td></tr>" .
                    "<tr><td>" . $_POST['country'] . "<br/></td></tr>" .
                    "<tr><td>Message :<br/>====================</td></tr>" .
                    "</table>";
                $mail->html = $message;
                $mail->envoi();
            }
            
            $this->redirect("Intranet/index");
        } else {
            $translate = Registry::get("translate");
            $this->_view['translate'] = $translate;
            $this->_view['msg'] = $translate->translate('fields_empty');
            $this->display(json_encode(array("status" => 2, "reponse" => $translate->translate('fields_empty'))));
        }
    }
    
    
    // renvoi les régions correspondant au pays choisi
    // ===============================================
    public function list_regionsAction()
    {
        $res = "<option></option>";
        if (isset($_POST['country']) && !empty($_POST['country']))
        {
            $db = Registry::get('db');
            $regions = $db->select("SELECT Region_id, Name FROM regions WHERE Country='" . $_POST['country'] . "' ORDER BY Name ASC");
            
            if (count($regions) > 0) {
                for($i=0; $i<count($regions); $i++) {
                    $res .= "<option value=" . $regions[$i]['Region_id'] . ">" . htmlentities($regions[$i]['Name']) . "</option>";
                }
            } else {
                $res .= "<option value=-1>Other</option>";
            }
        }
        $this->display($res);
    }


    public function infosAction()
    {
        // controle si l'utilisateur est connecté
        if (!Auth::hasIdentity())    {   $this->redirect("Index/index");      }

        $this->_view['translate'] = Registry::get("translate");
        $this->render('informations/public_news');
    }


    public function shareAction()
    {
        // controle si l'utilisateur est connecté
        if (!Auth::hasIdentity()) {
            $this->redirect("Index/index");
        }

        $this->_view['translate'] = Registry::get("translate");
        $this->render('admin/dev_inprogress');
    }


    public function communicateAction()
    {
        // controle si l'utilisateur est connecté
        if (!Auth::hasIdentity())    {   $this->redirect("Index/index");      }

        $this->_view['translate'] = Registry::get("translate");
        $this->render('informations/public_news');
    }


    public function financeAction()
    {
        // controle si l'utilisateur est connecté
        if (!Auth::hasIdentity())    {   $this->redirect("Index/index");      }

        $this->_view['translate'] = Registry::get("translate");
        $this->render('admin/dev_inprogress');
    }
    
    
    public function consoleAction()
    {
        // controle d'accès à la console
        if (!Auth::hasIdentity())       {   $this->redirect("Index/index");     }
        if (!Member::EstMembre(1))     {   $this->redirect("Intranet/index");  }
        
        // Journal de log
        $member = Member::getInstance();
        $db_log = new Log("db");
        $db_log->log($member->identite . " entre dans la console d'administration.", Log::INFO);
        
        $this->_view['translate'] = Registry::get("translate");
        $this->redirect('Admin/display_console');
    }

}