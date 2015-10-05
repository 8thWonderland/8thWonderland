<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Group;

class GroupRepository extends AbstractRepository {
    public function findGroups() {
        return $this->connection->select(
            'SELECT g.id, g.name, g.description, u.identity, g.created_at, g.updated_at, gt.label ' .
            'FROM groups g ' .
            'INNER JOIN group_types gt ON gt.id = g.type_id ' .
            'INNER JOIN users u ON u.id = g.contact_id ' .
            'ORDER BY g.name ASC'
        );
    }
    
    public function findRegionalGroups() {
        return $this->connection->select(
            'SELECT g.id, g.name, g.description, u.identity, g.created_at, g.updated_at, gt.label, r.Longitude, r.Latitude ' .
            'FROM groups g ' .
            'INNER JOIN group_types gt ON gt.id = g.type_id ' .
            'INNER JOIN users u ON u.id = g.contact_id ' .
            'INNER JOIN regions r ON r.Name = g.name ' .
            'WHERE r.Longitude IS NOT NULL AND r.Latitude IS NOT NULL ' .
            'ORDER BY g.name ASC'
        );
    }
    
    public function countGroupMembers($groupId) {
        return $this->connection->query(
            "SELECT COUNT(*) AS count FROM citizen_groups WHERE group_id = $groupId"
        )->fetch_assoc()['count'];
    }
    
    public function findGroupMembers($groupId) {
        return $this->connection->select(
            'SELECT u.id, u.identity, u.last_connected_at ' .
            'FROM citizen_groups cg ' .
            'INNER JOIN users u ON cg.citizen_id = u.id ' .
            "WHERE cg.group_id = $groupId " .
            'ORDER BY u.identity ASC'
        );
    }
    
    public function update(Group $group) {
        
    }
    
    public function create(Group $group) {
        
    }
}