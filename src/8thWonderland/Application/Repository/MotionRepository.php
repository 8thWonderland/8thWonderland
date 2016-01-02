<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;
use Wonderland\Application\Model\MotionTheme;

class MotionRepository extends AbstractRepository {
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param boolean $raw
     * @return array|Member
     */
    public function getActiveMotions(Member $member, $raw = true) {
        $statement = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.created_at, m.ended_at, !ISNULL(mvt.citizen_id) as has_already_voted ' .
            'FROM motions m ' .
            'LEFT JOIN motions_vote_tokens mvt ON mvt.motion_id = m.id AND mvt.citizen_id = :citizen_id ' .
            'WHERE m.ended_at > NOW() ' .
            'ORDER BY m.ended_at DESC '
        , ['citizen_id' => $member->getId()]);
        
        $result = [];
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = 
                ($raw)
                ? $this->formatArray($data)
                : $this->formatObject($data)
            ;
        }
        return $result;
    }
    
    /**
     * @return array
     */
    public function getMotionThemes() {
        return $this->connection->query(
            'SELECT id, label, duration FROM motion_themes'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @param int $motionThemeId
     * @return array|null
     */
    public function getMotionTheme($motionThemeId) {
        $data = $this->connection->prepareStatement(
            'SELECT id, label, duration FROM motion_themes WHERE id = :id'
        , ['id' => $motionThemeId])->fetch(\PDO::FETCH_ASSOC);
        
        if($data === false) {
            return null;
        }
        return
            (new MotionTheme())
            ->setId($data['id'])
            ->setLabel($data['label'])
            ->setDuration($data['duration'])
        ;
    }
    
    /**
     * @param \Wonderland\Application\Model\Motion $motion
     */
    public function createMotion(Motion &$motion) {
        $affectedRows = $this->connection->prepareStatement(
            'INSERT INTO motions(theme_id, title, description, means, author_id, ' .
            'created_at, ended_at, is_active, is_approved) VALUES(:theme_id, :title, ' .
            ':description, :means, :author_id, :created_at, :ended_at, :is_active, :is_approved)'
        , [
            'theme_id' => $motion->getTheme()->getId(),
            'title' => $motion->getTitle(),
            'description' => $motion->getDescription(),
            'means' => $motion->getMeans(),
            'author_id' => $motion->getAuthor()->getId(),
            'created_at' => $motion->getCreatedAt()->format('c'),
            'ended_at' => $motion->getEndedAt()->format('c'),
            'is_active' => $motion->getIsActive(),
            'is_approved' => $motion->getIsApproved()
        ])->rowCount();
        if($affectedRows === 0) {
            throw new \PDOException($this->connection->errorInfo()[2], $this->connection->errorCode());
        }
        $motion->setId($this->connection->lastInsertId());
    }
    
    /**
     * @param int $motionId
     * @return \Wonderland\Application\Model\Motion|null
     */
    public function getMotion($motionId) {
        $data = $this->connection->prepareStatement(
            'SELECT m.id, m.title, mt.id as theme_id, mt.label as theme_label, ' .
            'm.description, m.means, m.created_at, m.ended_at, m.is_active, m.is_approved, ' .
            'm.score, u.id as author_id, u.identity as author_identity ' .
            'FROM motions m '.
            'INNER JOIN motion_themes mt ON mt.id = m.theme_id ' .
            'INNER JOIN users u ON m.author_id = u.id ' .
            'WHERE m.id = :motion_id'
        , ['motion_id' => $motionId])->fetch(\PDO::FETCH_ASSOC);
        
        if($data === false) {
            return null;
        }
        return $this->formatObject($data);
    }
    
    
    /**
     * @param Motion $motion
     */
    public function getVotes(Motion $motion) {
        $data = $this->connection->prepareStatement(
            'SELECT COUNT(mv1.hash) AS positive_votes, COUNT(mv2.hash) as negative_votes ' .
            'FROM motions_votes WHERE Motion_id = $motionId AND Choix = 1'
        )->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @param array $data
     * @return array
     */
    public function formatArray($data) {
        $result = [
            'id' => $data['id'],
            'title' => $data['title'],
            'created_at' => $data['created_at'],
            'ended_at' => $data['ended_at']
        ];
        
        if(isset($data['has_already_voted'])) {
            $result['has_already_voted'] = $data['has_already_voted'];
        }
        
        return $result;
    }
    
    /**
     * @param array $data
     * @return Member
     */
    public function formatObject($data) {
        return
            (new Motion())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setMeans($data['means'])
            ->setIsActive($data['is_active'])
            ->setIsApproved($data['is_approved'])
            ->setScore($data['score'])
            ->setTheme(
                (new MotionTheme())
                ->setId($data['theme_id'])
                ->setLabel($data['theme_label'])
            )
            ->setAuthor(
                (new Member())
                ->setId($data['author_id'])
                ->setIdentity($data['author_identity'])
            )
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setEndedAt(new \DateTime($data['ended_at']))
        ;
    }
}