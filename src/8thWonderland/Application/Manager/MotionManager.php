<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Application;

class MotionManager {
    protected $application;
    
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * Return motions which are currently being voted
     * 
     * @return string
     */
    public function displayActiveMotions() {
        $translate = $this->application->get('translate');
        $memberId = $this->application->get('session')->get('__id__');
        
        $montionsList = $this->application->get('mysqli')->query(
            'SELECT motion_id, title_key, date_fin_vote ' .
            'FROM Motions ' .
            'WHERE Date_fin_vote >  NOW() ' .
            'ORDER BY date_fin_vote DESC '
        );
        if ($montionsList->num_rows > 0) {
            $response = '';
            while ($motion = $montionsList->fetch_assoc()) {
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
        return "<tr><td>{$translate->translate('no_result')}</td></tr>";
    }
    
    /**
     * @return array
     */
    public function displayMotions() {
        return $this->application->get('mysqli')->select(
            'SELECT Motion_id, Title_key, Label_key, Submission_date, Date_fin_vote, Citizen_id ' .
            'FROM Motions, Motions_Themes ' .
            'WHERE Date_fin_vote < NOW() AND Motions.Theme_id=Motions_Themes.Theme_id ' .
            'ORDER BY motion_id DESC'
        );
    }
    
    /**
     * Récupération des détails d'une motion
     * 
     * @param int $id
     * @return array
     */
    public function displayMotionDetails($id)
    {
        $motion = $this->application->get('mysqli')->select(
            'SELECT motion_id, title_key, label_key, description, moyens, submission_date, date_fin_vote ' .
            'FROM Motions, Motions_Themes ' .
            "WHERE motion_id = $id AND Motions.theme_id=Motions_Themes.Theme_id"
        );
        $motion[0]['vote'] = $this->getVotes($id);
        return $motion;
    }
    
    /**
     * @return array
     */
    public function getMotionThemes()
    {
        return $this->application->get('mysqli')->query(
            'SELECT label_key FROM Motions_Themes ORDER BY label_key ASC'
        );
    }
    
    /**
     * @param int $motionId
     * @return array
     */
    public function getVotes($motionId)
    {
        $db = $this->application->get('mysqli');
        return [
            $db->count('Motions_Votes', " WHERE Motion_id = $motionId AND Choix = 1"),
            $db->count('Motions_Votes', " WHERE Motion_id = $motionId AND Choix = 2")
        ];
    }
    
    /**
     * @param string $title
     * @param string $theme
     * @param string $description
     * @param string $means
     * @return \mysqli_result
     */
    public function validateMotion($title, $theme, $description, $means) {
        return $this->application->get('mysqli')->query(
            'INSERT INTO Motions ' .
            "(Theme_id, Title_key, Description, Moyens, Submission_date, Date_fin_vote, Citizen_id) " .
            "values ('" . htmlentities(utf8_decode($theme), ENT_QUOTES) . "', " . 
            "'" . htmlentities(utf8_decode($title), ENT_QUOTES) . "', " .
            "'" . nl2br(htmlentities($description)) . "', '" . nl2br(htmlentities($means)) . "',  NOW(), " .
            "DATE_ADD(NOW(), INTERVAL (SELECT Duree FROM Motions_Themes WHERE Motions_Themes.Theme_id = " . $theme . ") DAY), " .
            $this->application->get('auth')->getIdentity() . ")"
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
        $db     = $this->application->get('mysqli');
        $member = $this->application->get('auth')->getIdentity();
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
        if ($db->affected_rows == 0) {
            return $db->affected_rows;
        }
        
        $choice = ($vote === 'approved') ? 1 : 2;
        
        $hash = hash('sha512', "{$db->insert_id}#$id#$member#$choice#$date#$ip");
        $db->query(
            'INSERT INTO Motions_Votes ' .
            '(Motion_id, Choix, Hash) ' .
            "VALUES ($id, '$choice', '$hash')"
        );
        return $db->affected_rows;
    }
    
    /**
     * @param int $motionId
     * @param int $memberId
     * @return int
     */
    protected function hasAlreadyVoted($motionId, $memberId) {
        return $this
            ->application
            ->get('mysqli')
            ->count("Motions_Votes_Jetons", " WHERE Motion_id = $motionId AND Citizen_id = $memberId")
        ;
    }
    
    public function checkMotion(){
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