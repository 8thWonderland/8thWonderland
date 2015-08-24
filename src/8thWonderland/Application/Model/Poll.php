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
    /**
     * Return motions which are currently being voted
     * 
     * @TODO: Return only data without any notion of view
     * 
     * @return string
     */
    public function displayActiveMotions()
    {
        $translate = Registry::get('translate');
        $auth = Auth::getInstance();
        $id_member = $auth->getIdentity();
        
        $list_motions = Registry::get('db')->query(
            'SELECT motion_id, title_key, date_fin_vote ' .
            'FROM Motions ' .
            'WHERE Date_fin_vote >  NOW() ' .
            'ORDER BY date_fin_vote DESC '
        );
        if ($list_motions->num_rows > 0) {
            $response = '';
            while ($motion = $list_motions->fetch_assoc()) {
                $response .=
                    "<tr><td><a onclick=\"Clic('/Motion/display_motion', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">{$motion['title_key']}</a></td>" .
                    "<td><a onclick=\"Clic('/Motion/display_motion', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">{$motion['date_fin_vote']}</a></td>"
                ;
                if ($this->hasAlreadyVoted($motion['motion_id'], $memberId) == 0) {
                    $response .=
                        "<td><div class='bouton'><a onclick=\"Clic('/Motion/display_vote', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">" .
                        "<span style='color: #dfdfdf;'>{$translate->translate('btn_votemotion')}</span></a></div></td>"
                    ;
                }
                $response .= '</tr>';
            }
            return $response;
        }
        else {
            $reponse = "<tr><td>" . $translate->translate('no_result') . "</td></tr>";
        }

        return $reponse;
    }
    
    /**
     * @return array
     */
    public function displayMotions() {
        return Registry::get('db')->select(
            'SELECT Motion_id, Title_key, Label_key, Submission_date, Date_fin_vote, Citizen_id ' .
            'FROM Motions, Motions_Themes ' .
            'WHERE Date_fin_vote < NOW() AND Motions.Theme_id=Motions_Themes.Theme_id ' .
            'ORDER BY motion_id DESC'
        );
    }
    
    // Récupération des détails d'une motion
    // =====================================
    public function displayMotionDetails($id)
    {
        $motion = Registry::get('db')->select(
            'SELECT motion_id, title_key, label_key, description, moyens, submission_date, date_fin_vote ' .
            'FROM Motions, Motions_Themes ' .
            "WHERE motion_id = $id AND Motions.theme_id=Motions_Themes.Theme_id"
        );
        $motion[0]['vote'] = $this->_getVotes($id);
        return $motion;
    }
    
    
    // Récupération des thèmes de motions
    // ==================================
    public static function _getThemes()
    {
        return Registry::get('db')->query(
            'SELECT label_key FROM Motions_Themes ORDER BY label_key ASC'
        );
    }
    
    
    // Récupération du nombre de votes approuvés et rejetés
    // ====================================================
    public static function _getVotes($id_motion)
    {
        $db = Registry::get('db');
        return [
            $db->count('Motions_Votes', " WHERE Motion_id=$id_motion AND Choix=1"),
            $db->count('Motions_Votes', " WHERE Motion_id=$id_motion AND Choix=2")
        ];
    }
    
    /**
     * 
     * @param type $p_title
     * @param type $p_theme
     * @param type $p_description
     * @param type $p_means
     * @return type
     */
    public function validMotion($p_title, $p_theme, $p_description, $p_means)
    {
        $auth   = Auth::getInstance();
        
        $theme = htmlentities(utf8_decode($p_theme), ENT_QUOTES);
        $title = htmlentities(utf8_decode($p_title), ENT_QUOTES);
        $description = nl2br(htmlentities($p_description));
        $means = nl2br(htmlentities($p_means));
        $author = $auth->getIdentity();
                
        return Registry::get('db')->query(
            "INSERT INTO Motions " .
            "(Theme_id, Title_key, Description, Moyens, Submission_date, Date_fin_vote, Citizen_id) " .
            "values ('" . $theme . "', '" . $title . "', '" . $description . "', '" . $means . "',  NOW(), " .
            "DATE_ADD(NOW(), INTERVAL (SELECT Duree FROM Motions_Themes WHERE Motions_Themes.Theme_id = " . $theme . ") DAY), " .
            $author . ")"
        );
    }
    
    /**
     * Returns number of affected rows
     * 
     * @param int $id
     * @param string $vote
     * @return int
     */
    public function voteMotion($id, $vote)
    {
        $db     = Registry::get('db');
        $auth   = Auth::getInstance();
        $member = $auth->getIdentity();
        $date   = date('Y-m-d h-i-s');
        $ip =
            (isset($_SERVER['REMOTE_ADDR']))
            ? $_SERVER['REMOTE_ADDR']
            : 'inconnue'
        ;
        
        $db->query(
            'INSERT INTO Motions_Votes_Jetons ' .
            '(Motion_id, Citizen_id, Date, Ip) ' .
            "VALUES ($id, $member, '$date', '$ip')"
        );
        if ($db->affected_rows == 0)    {   return $db->affected_rows;  }
        
        $choice = ($vote === 'approved') ? 1 : 2;
        
        $hash = hash('sha512', "{$db->insert_id}#$id#$member#$choice#$date#$ip");
        $db->query(
            'INSERT INTO Motions_Votes ' .
            '(Motion_id, Choix, Hash) ' .
            "VALUES ($id, '$choice', '$hash')"
        );
        return $db->affected_rows;
    }
    
    
    // Test si le membre a déjà voté la motion
    // =======================================
    protected function hasAlreadyVoted($id_motion, $id_member)
    {
        return Registry::get('db')->count("Motions_Votes_Jetons", " WHERE Motion_id=" . $id_motion . " AND Citizen_id=" . $id_member);
    }
    
    
    function checkMotion(){
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
