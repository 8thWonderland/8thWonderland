<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Group;

class GroupRepository extends AbstractRepository {
    public function find($id) {
        return $this->connection->prepareStatement(
            'SELECT g.id, g.name, g.description, g.contact_id, u.identity, g.created_at, g.updated_at, gt.label ' .
            'FROM groups g ' .
            'INNER JOIN group_types gt ON gt.id = g.type_id ' .
            'INNER JOIN users u ON u.id = g.contact_id ' .
            'WHERE g.id = :id'
        , ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function findGroups() {
        return $this->connection->query(
            'SELECT g.id, g.name, g.description, u.identity, g.created_at, g.updated_at, gt.label ' .
            'FROM groups g ' .
            'INNER JOIN group_types gt ON gt.id = g.type_id ' .
            'INNER JOIN users u ON u.id = g.contact_id ' .
            'ORDER BY g.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findRegionalGroups() {
        return $this->connection->query(
            'SELECT g.id, g.name, g.description, u.identity, g.created_at, g.updated_at, gt.label, r.Longitude, r.Latitude ' .
            'FROM groups g ' .
            'INNER JOIN group_types gt ON gt.id = g.type_id ' .
            'INNER JOIN users u ON u.id = g.contact_id ' .
            'INNER JOIN regions r ON r.Name = g.name ' .
            'WHERE r.Longitude IS NOT NULL AND r.Latitude IS NOT NULL ' .
            'ORDER BY g.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function countGroupMembers($groupId) {
        return $this->connection->prepareStatement(
            'SELECT COUNT(*) AS count FROM citizen_groups WHERE group_id = :group_id'
        , ['group_id' => $groupId])->fetch(\PDO::FETCH_ASSOC)['count'];
    }
    
    public function findGroupMembers($groupId) {
        return $this->connection->prepareStatement(
            'SELECT u.id, u.identity, u.last_connected_at ' .
            'FROM citizen_groups cg ' .
            'INNER JOIN users u ON cg.citizen_id = u.id ' .
            'WHERE cg.group_id = :group_id ' .
            'ORDER BY u.identity ASC'
        , ['group_id' => $groupId])->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function update(Group $group) {
        return $this->connection->prepareStatement(
            'UPDATE groups SET type_id = :type_id, description = :description, ' .
            'name = :name, contact_id = :contact_id, updated_at = :updated_at WHERE id = :id'
        , [
            'id' => $group->getId(),
            'type_id' => $group->getType()->getId(),
            'description' => $group->getDescription(),
            'name' => $group->getName(),
            'contact_id' => $group->getContact()->getId(),
            'updated_at' => (new \DateTime())->format('c')
        ]);
    }
}