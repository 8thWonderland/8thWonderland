<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;

use Wonderland\Library\Exception\NotFoundException;

use Wonderland\Library\Database\PdoDriver;

use Wonderland\Application\Repository\MotionRepository;

use Wonderland\Library\Translator;

class MotionManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    /** @var \Wonderland\Application\Repository\MotionRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     * @param \Wonderland\Library\Translator $translator
     * @param \Wonderland\Application\Repository\MotionRepository $repository
     */
    public function __construct(PdoDriver $connection, Translator $translator, MotionRepository $repository) {
        $this->connection = $connection;
        $this->translator = $translator;
        $this->repository = $repository;
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param int $themeId
     * @param \Wonderland\Application\Model\Member $author
     * @param string $means
     * @return \Wonderland\Application\Model\Motion
     * @throws \Wonderland\Library\Exception\NotFoundException
     */
    public function createMotion($title, $description, $themeId, Member $author, $means) {
        if(($theme = $this->repository->getMotionTheme($themeId)) === null) {
            throw new NotFoundException('Motion Theme Not Found');
        }
        
        $endedAt = new \DateTime();
        $endedAt->add(new \DateInterval("P{$theme->getDuration()}D"));
        
        $motion =
            (new Motion())
            ->setTitle($title)
            ->setDescription($description)
            ->setMeans($means)
            ->setAuthor($author)
            ->setTheme($theme)
            ->setIsActive(1)
            ->setIsApproved(0)
            ->setCreatedAt(new \DateTime())
            ->setEndedAt($endedAt)
        ;
        $this->repository->createMotion($motion);
        return $motion;
    }
    
    /**
     * @param Member $member
     * @return array
     */
    public function getActiveMotions(Member $member) {
        return $this->repository->getActiveMotions($member);
    }
    
    /**
     * @return array
     */
    public function displayMotions() {
        return $this->connection->query(
            'SELECT m.Motion_id, m.Title_key, mt.Label_key, m.Submission_date, m.Date_fin_vote, u.identity ' .
            'FROM motions m ' .
            'INNER JOIN motions_themes mt ON m.Theme_id = mt.Theme_id ' .
            'LEFT JOIN users u ON u.id = m.Citizen_id ' .
            'WHERE m.Date_fin_vote < NOW() ' .
            'ORDER BY m.motion_id DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);
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
        )->fetchAll(\PDO::FETCH_ASSOC);
        $motion[0]['vote'] = $this->getVotes($id);
        return $motion;
    }
    
    /**
     * @return array
     */
    public function getMotionThemes() {
        return $this->repository->getMotionThemes();
    }
    
    /**
     * @param int $motionId
     * @return array
     */
    public function getVotes($motionId) {
        return [
            $this->connection->query(
                "SELECT COUNT(*) AS count FROM motions_votes WHERE Motion_id = $motionId AND Choix = 1"
            )->fetch(\PDO::FETCH_ASSOC)['count'],
            $this->connection->query(
                "SELECT COUNT(*) AS count FROM motions_votes WHERE Motion_id = $motionId AND Choix = 2"
            )->fetch(\PDO::FETCH_ASSOC)['count']
        ];
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
        $statement = $this->connection->prepareStatement(
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
        return $this->connection->query(
            "SELECT COUNT(*) AS count FROM motions_votes_jetons WHERE Motion_id = $motionId AND Citizen_id = $memberId"
        )->fetch(\PDO::FETCH_ASSOC)['count'];
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