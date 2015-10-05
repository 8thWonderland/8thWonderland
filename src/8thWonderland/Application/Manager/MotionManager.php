<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Database\PdoDriver;
use Wonderland\Library\Translator;

class MotionManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     * @param \Wonderland\Library\Translator $translator
     */
    public function __construct(PdoDriver $connection, Translator $translator) {
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
            'FROM motions ' .
            'WHERE Date_fin_vote >  NOW() ' .
            'ORDER BY date_fin_vote DESC '
        );
        $response = '';
        while ($motion = $motions->fetch()) {
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
        if(!empty($response)) {
            return $response;
        }
        return "<tr><td>{$this->translator->translate('no_result')}</td></tr>";
    }
    
    /**
     * @return array
     */
    public function displayMotions() {
        return $this->connection->query(
            'SELECT Motion_id, Title_key, Label_key, Submission_date, Date_fin_vote, Citizen_id ' .
            'FROM motions, motions_themes ' .
            'WHERE Date_fin_vote < NOW() AND motions.Theme_id = motions_themes.Theme_id ' .
            'ORDER BY motion_id DESC'
        )->fetchAll();
    }
    
    /**
     * Récupération des détails d'une motion
     * 
     * @param int $id
     * @return array
     */
    public function displayMotionDetails($id) {
        $motion = $this->connection->query(
            'SELECT motion_id, title_key, label_key, description, moyens, submission_date, date_fin_vote ' .
            'FROM motions, motions_themes ' .
            "WHERE motion_id = $id AND motions.theme_id = motions_themes.Theme_id"
        )->fetchAll();
        $motion[0]['vote'] = $this->getVotes($id);
        return $motion;
    }
    
    /**
     * @return array
     */
    public function getMotionThemes() {
        return $this->connection->query(
            'SELECT theme_id, label_key FROM motions_themes ORDER BY label_key ASC'
        );
    }
    
    /**
     * @param int $motionId
     * @return array
     */
    public function getVotes($motionId) {
        return [
            $this->connection->query(
                "SELECT COUNT(*) AS count FROM motions_votes WHERE Motion_id = $motionId AND Choix = 1"
            )->fetch()['count'],
            $this->connection->query(
                "SELECT COUNT(*) AS count FROM motions_votes WHERE Motion_id = $motionId AND Choix = 2"
            )->fetch()['count']
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
        return $this->connection->prepareStatement(
            'INSERT INTO motions ' .
            "(Theme_id, Title_key, Description, Moyens, Submission_date, Date_fin_vote, Citizen_id) " .
            "values (':theme_id', ':title', :description, :means,  NOW(), " .
            'DATE_ADD(NOW(), INTERVAL (SELECT Duree FROM motions_themes ' .
            "WHERE motions_themes.Theme_id = :theme_id) DAY), :citizen_id)"
        , [
            'theme_id' => $theme,
            'title' => $title,
            'description' => $description,
            'means' => $means,
            'citizen_id' => $member->getId()
        ]);
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
        $statement = $this->connection->query(
            'INSERT INTO motions_votes_jetons (Motion_id, Citizen_id, Date, Ip) ' .
            'VALUES (:motion_id, :citizen_id, :date, :ip)'
        , [
            'motion_id' => $id,
            'citizen_id' => $member->getId(),
            'date' => $date,
            'ip' => $ip
        ]);
        if ($statement->rowCount() === 0) {
            return 0;
        }
        
        $choice = ($vote === 'approved') ? 1 : 2;
        
        $hash = hash('sha512', "{$this->connection->lastInsertId()}#$id#{$member->getIdentity()}#$choice#$date#$ip");
        $statement = $this->connection->prepareStatement(
            'INSERT INTO motions_votes(Motion_id, Choix, Hash)  VALUES (:id, :choice, :hash)'
        , [
            'id' => $id,
            'choice' => $choice,
            'hash' => $hash
        ]);
        return $statement->rowCount();
    }
    
    /**
     * @param int $motionId
     * @param int $memberId
     * @return int
     */
    protected function hasAlreadyVoted($motionId, $memberId) {
        return $this
            ->connection
            ->query(
                "SELECT COUNT(*) AS count FROM motions_votes_jetons WHERE Motion_id = $motionId AND Citizen_id = $memberId"
            )
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