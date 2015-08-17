<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Memory\Registry;
use Wonderland\Library\Auth;

/**
 * class polls
 *
 * Gestion des informations et opérations liées aux motions
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 */
class Poll
{
    // Affichage des motions en cours
    // ==============================
    public function display_motionsinprogress()
    {
        $db = Registry::get('db');
        $translate = Registry::get("translate");
        $auth = Auth::getInstance();
        $id_member = $auth->_getIdentity();
        $reponse = '';
        
        $req = "SELECT motion_id, title_key, date_fin_vote " .
               "FROM Motions " .
               "WHERE Date_fin_vote >  NOW() " .
               "ORDER BY date_fin_vote DESC ";
        
        $list_motions = $db->_query($req);
        if ($list_motions->num_rows > 0) {
            while ($motion = $list_motions->fetch_assoc()) {
                $reponse .= "<tr><td><a onclick=\"Clic('/motions/display_motion', 'motion_id=" . $motion['motion_id'] . "', 'milieu_milieu'); return false;\">" . $motion['title_key'] . "</a></td>" .
                            "<td><a onclick=\"Clic('/motions/display_motion', 'motion_id=" . $motion['motion_id'] . "', 'milieu_milieu'); return false;\">" . $motion['date_fin_vote'] . "</a></td>";
                if ($this->poll_active($motion['motion_id'], $id_member) == 0) {
                    $reponse .= "<td><div class='bouton'><a onclick=\"Clic('/motions/display_vote', 'motion_id=" . $motion['motion_id'] . "', 'milieu_milieu'); return false;\">" .
                                "<span style='color: #dfdfdf;'>" . $translate->msg('btn_votemotion') . "</span></a></div></td>";
                }
                $reponse .= "</tr>";
            }
        }
        else {
            $reponse = "<tr><td>" . $translate->msg('no_result') . "</td></tr>";
        }

        return $reponse;
    }
    
    
    // Affichage des motions archivées
    // ===============================
    public function display_motions()
    {
        $db = Registry::get('db');

        $req = "SELECT Motion_id, Title_key, Label_key, Submission_date, Date_fin_vote, Citizen_id " .
               "FROM Motions, Motions_Themes " .
               "WHERE Date_fin_vote < NOW() AND Motions.Theme_id=Motions_Themes.Theme_id " .
               "ORDER BY motion_id DESC";

        return $db->select($req);
    }
    
    
    // Récupération des détails d'une motion
    // =====================================
    public function display_detailsmotion($id)
    {
        $db = Registry::get('db');
        $req = "SELECT motion_id, title_key, label_key, description, moyens, submission_date, date_fin_vote " .
           "FROM Motions, Motions_Themes " .
           "WHERE motion_id = " . $id . " AND Motions.theme_id=Motions_Themes.Theme_id";
        $motion = $db->select($req);
        $motion[0]['vote'] = $this->_getVotes($id);
        return $motion;
    }
    
    
    // Récupération des thèmes de motions
    // ==================================
    public static function _getThemes()
    {
        $db = Registry::get('db');
        $req = "SELECT label_key " .
               "FROM Motions_Themes " .
               "ORDER BY label_key ASC";
        return $db->_query($req);
    }
    
    
    // Récupération du nombre de votes approuvés et rejetés
    // ====================================================
    public static function _getVotes($id_motion)
    {
        $db = Registry::get('db');
        $approved = $db->count("Motions_Votes", " WHERE Motion_id=" . $id_motion . " AND Choix=1");
        $refused = $db->count("Motions_Votes", " WHERE Motion_id=" . $id_motion . " AND Choix=2");
        return array($approved, $refused);
    }
    
    
    // Valide le dépot d'une motion
    // ============================
    public function valid_motion($p_title, $p_theme, $p_description, $p_means)
    {
        $db     = Registry::get('db');
        $auth   = Auth::getInstance();
        
        $theme = htmlentities(utf8_decode($p_theme), ENT_QUOTES);
        $title = htmlentities(utf8_decode($p_title), ENT_QUOTES);
        $description = htmlentities($p_description);
        $means = htmlentities($p_means);
        $author = $auth->_getIdentity();
                
        $req = "INSERT INTO Motions " .
               "(Theme_id, Title_key, Description, Moyens, Submission_date, Date_fin_vote, Citizen_id) " .
               "values ('" . $theme . "', '" . $title . "', '" . nl2br($description) . "', '" . nl2br($means) . "',  NOW(), " .
               "DATE_ADD(NOW(), INTERVAL (SELECT Duree FROM Motions_Themes WHERE Motions_Themes.Theme_id = " . $theme . ") DAY), " .
               $author . ")";
        return $db->_query($req);
    }
    
    
    // Enregistre le vote d'un membre
    // ==============================
    public function vote_motion($id, $vote)
    {
        $db     = Registry::get('db');
        $auth   = Auth::getInstance();
        $member = $auth->_getIdentity();
        $date   = date('Y-m-d h-i-s');
        (isset($_SERVER['REMOTE_ADDR']))?$ip = $_SERVER['REMOTE_ADDR']:$ip='inconnue';
        
        // Enregistrement du jeton de vote
        $req = "INSERT INTO Motions_Votes_Jetons " .
               "(Motion_id, Citizen_id, Date, Ip) " .
               "VALUES (" . $id . ", " . $member . ", '" . $date . "', '" . $ip . "')";
        $db->_query($req);
        if ($db->affected_rows == 0)    {   return $db->affected_rows;  }
        
        $choix = ($vote=='approved')?1:2;
        
        // Enregistrement du résultat de vote
        $hash = hash("sha512",$db->insert_id."#".$id."#".$member."#".$choix."#".$date."#".$ip);
        $req = "INSERT INTO Motions_Votes " .
               "(Motion_id, Choix, Hash) " .
               "VALUES (" . $id . ", '" . $choix . "', '" . $hash . "')";
        $db->_query($req);
        return $db->affected_rows;
    }
    
    
    // Test si le membre a déjà voté la motion
    // =======================================
    protected function poll_active($id_motion, $id_member)
    {
        $db     = Registry::get('db');
        return $db->count("Motions_Votes_Jetons", " WHERE Motion_id=" . $id_motion . " AND Citizen_id=" . $id_member);
    }
    
    
    function check_motion(){
        // vérification des votes
        // en commentaire car c'est un code sensible à n'utiliser qu'en cas de crise
    /*
        $db = memory_registry::get('db');

        $req = "SELECT * ".
                  "FROM Motions_Votes_Jetons, Motions_Votes".
                  " WHERE Motions_Votes_id = Motions_Votes_Jetons_id";
        $motions = $db->select($req);

        foreach($motions as $motion){
            $motion['Date'] = date("Y-m-d h-i-s", strtotime($motion['Date']));
            echo "<br/>";
            $vote= ($motion['Choix'] == 1 ? 'approved' : 'refused');
        
            $hash = hash("sha512",$motion['Motions_Votes_id']."#".$motion['Motion_id']."#".$motion['Citizen_id']."#".($motion['Choix'] == 1 ? 'approved' : 'refused')."#".$motion['Date']."#".$motion['Ip']);
            if($hash != $motion['Hash'] ){
                echo $motion['Motions_Votes_id']." err:<br/>\n";
                echo $hash." != ".$motion['Hash']."<br/>\n";
                $hash2 = hash("sha512",$motion['Motions_Votes_id']."#".$motion['Motion_id']."#".$motion['Citizen_id']."#".($motion['Choix'] == 1 ? 'refused' : 'approved')."#".$motion['Date']."#".$motion['Ip']);
                if($hash2!= $motion['Hash']){
                    echo $hash2." != ".$motion['Hash']."<br/>\n";
                }else{
                   $req2 = "UPDATE Motions_Votes SET Choix = 2 WHERE Motions_Votes_id = ".$motion['Motions_Votes_id'];
                //   $db->_query($req2); // ATTENTION LÀ C'EST SUPER DÉLICAT. IL FAUT ÊTRE SÛR À 100% QUE C'EST BON.
                    
                    echo $motion['Motions_Votes_id']." # 2 et non 1<br>\n";
                }
            }else{
                echo $motion['Motions_Votes_id']." ok<br/>\n";
            }
        }
        */
    }   
}
