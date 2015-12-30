<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;

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