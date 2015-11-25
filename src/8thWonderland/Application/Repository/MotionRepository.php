<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;

class MotionRepository extends AbstractRepository {
    
    public function getActiveMotions(Member $member, $raw = true) {
        $statement = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.ended_at, COUNT(mvj.*) as has_already_voted ' .
            'FROM motions m ' .
            'INNER JOIN motions_votes_jetons mvj ON mvj.motion_id = m.id AND mvj.citizen_id = :citizen_id' .
            'WHERE m.ended_at >  NOW() ' .
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
    
    public function formatObject($data) {
        return
            (new Motion())
            ->setId($data['id'])
        ;
    }
}