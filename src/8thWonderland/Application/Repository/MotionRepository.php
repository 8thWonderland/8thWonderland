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
            'SELECT m.id, m.title, m.created_at, m.ended_at, COUNT(mvt.citizen_id) as has_already_voted ' .
            'FROM motions m ' .
            'INNER JOIN motions_vote_tokens mvt ON mvt.motion_id = m.id AND mvt.citizen_id = :citizen_id ' .
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
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setEndedAt(new \DateTime($data['ended_at']))
        ;
    }
}