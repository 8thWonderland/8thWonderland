<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Database\Mysqli;
use Wonderland\Library\Translator;

class MotionManager {
    /** @var \Wonderland\Library\Database\Mysqli **/
    protected $connection;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    
    /**
     * @param \Wonderland\Library\Database\Mysqli $connection
     * @param \Wonderland\Library\Translator $translator
     */
    public function __construct(Mysqli $connection, Translator $translator) {
        $this->connection = $connection;
        $this->translator = $translator;
    }
    
    /**
     * Return motions which are currently being voted
     * 
     * @param \Wonderland\Application\Model\Member
     * @return string
     */
    public function displayActiveMotions(Member $member) {
        $motions = $this->connection->query(
            'SELECT motion_id, title_key, date_fin_vote ' .
            'FROM Motions ' .
            'WHERE Date_fin_vote >  NOW() ' .
            'ORDER BY date_fin_vote DESC '
        );
        if ($motions->num_rows === 0) {
            return "<tr><td>{$this->translator->translate('no_result')}</td></tr>";
        }
        $response = '';
        while ($motion = $motions->fetch_assoc()) {
            $response .=
                "<tr><td><a onclick=\"Clic('/Motion/displayMotion', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">{$motion['title_key']}</a></td>" .
                "<td><a onclick=\"Clic('/Motion/displayMotion', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">{$motion['date_fin_vote']}</a></td>"
            ;
            if ($this->hasAlreadyVoted($motion['motion_id'], $member->getId()) == 0) {
                $response .=
                    "<td><div class='bouton'><a onclick=\"Clic('/Motion/displayVote', 'motion_id={$motion['motion_id']}', 'milieu_milieu'); return false;\">" .
                    "<span style='color: #dfdfdf;'>{$this->translator->translate('btn_votemotion')}</span></a></div></td>"
                ;
            }
            $response .= '</tr>';
        }
        return $response;
    }
    
    /**
     * @return array
     */
    public function displayMotions() {
        return $this->connection->select(
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
    public function displayMotionDetails($id) {
        $motion = $this->connection->select(
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
    public function getMotionThemes() {
        return $this->connection->query(
            'SELECT theme_id, label_key FROM Motions_Themes ORDER BY label_key ASC'
        );
    }
    
    /**
     * @param int $motionId
     * @return array
     */
    public function getVotes($motionId) {
        return [
            $this->connection->count('Motions_Votes', " WHERE Motion_id = $motionId AND Choix = 1"),
            $this->connection->count('Motions_Votes', " WHERE Motion_id = $motionId AND Choix = 2")
        ];
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param string $title
     * @param string $theme
     * @param string $description
     * @param string $means
     * @return \mysqli_result
     */
    public function validateMotion(Member $member, $title, $theme, $description, $means) {
        return $this->connection->query(
            'INSERT INTO Motions ' .
            "(Theme_id, Title_key, Description, Moyens, Submission_date, Date_fin_vote, Citizen_id) " .
            "values ('" . htmlentities(utf8_decode($theme), ENT_QUOTES) . "', " . 
            "'" . htmlentities(utf8_decode($title), ENT_QUOTES) . "', " .
            "'" . nl2br(htmlentities($description)) . "', '" . nl2br(htmlentities($means)) . "',  NOW(), " .
            'DATE_ADD(NOW(), INTERVAL (SELECT Duree FROM Motions_Themes ' .
            "WHERE Motions_Themes.Theme_id = $theme) DAY), {$member->getId()})"
        );
    }
    
    /**
     * Returns number of affected rows
     * 
     * @param \Wonderland\Application\Model\Member $member
     * @param int $id
     * @param string $vote
     * @return int
     */
    public function voteMotion(Member $member, $id, $vote) {
        $date = date('Y-m-d h-i-s');
        $ip =
            (isset($_SERVER['REMOTE_ADDR']))
            ? $_SERVER['REMOTE_ADDR']
            : 'inconnue'
        ;
        
        $this->connection->query(
            'INSERT INTO Motions_Votes_Jetons ' .
            '(Motion_id, Citizen_id, Date, Ip) ' .
            "VALUES ($id, {$member->getId()}, '$date', '$ip')"
        );
        if ($this->connection->affected_rows == 0) {
            return $this->connection->affected_rows;
        }
        
        $choice = ($vote === 'approved') ? 1 : 2;
        
        $hash = hash('sha512', "{$this->connection->insert_id}#$id#$member#$choice#$date#$ip");
        $this->connection->query(
            'INSERT INTO Motions_Votes ' .
            '(Motion_id, Choix, Hash) ' .
            "VALUES ($id, '$choice', '$hash')"
        );
        return $this->connection->affected_rows;
    }
    
    /**
     * @param int $motionId
     * @param int $memberId
     * @return int
     */
    protected function hasAlreadyVoted($motionId, $memberId) {
        return $this
            ->connection
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