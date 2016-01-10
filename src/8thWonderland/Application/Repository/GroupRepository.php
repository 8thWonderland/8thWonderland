<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;
use Wonderland\Application\Model\Member;

class GroupRepository extends AbstractRepository
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function find($id)
    {
        $data = $this->connection->prepareStatement(
            'SELECT g.id, g.name, g.description, g.contact_id, u.identity as identity, g.is_public, g.created_at, g.updated_at, g.type_id, gt.label '.
            'FROM groups g '.
            'INNER JOIN group_types gt ON gt.id = g.type_id '.
            'LEFT JOIN users u ON u.id = g.contact_id '.
            'WHERE g.id = :id'
        , ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
        
        if($data === false) {
            return null;
        }
        return $this->formatObject($data);
    }

    /**
     * @param int  $typeId
     * @param int  $minRange
     * @param int  $maxRange
     * @param bool $raw
     *
     * @return array|Group
     */
    public function findGroups($typeId = null, $minRange = null, $maxRange = null, $raw = true)
    {
        $whereClause =
            ($typeId !== null)
            ? "WHERE gt.id = $typeId "
            : ''
        ;
        $statement = $this->connection->query(
            'SELECT g.id, g.name, g.description, u.identity, g.is_public, g.created_at, g.updated_at, gt.id as type_id, gt.label '.
            'FROM groups g '.
            'INNER JOIN group_types gt ON gt.id = g.type_id '.
            'LEFT JOIN users u ON u.id = g.contact_id '.
            $whereClause.
            'ORDER BY g.name ASC '.
            $this->getRangeStatements($minRange, $maxRange)
        );
        if($statement === false) {
            $this->throwPdoException();
        }
        $groups = [];
        while ($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $groups[] =
                ($raw)
                ? $data
                : $this->formatObject($data)
            ;
        }

        return $groups;
    }

    /**
     * @param int $memberId
     * @param int $groupId
     */
    public function addMemberToGroup($memberId, $groupId) {
        $affectedRows = $this->connection->prepareStatement(
            'INSERT INTO citizen_groups(citizen_id, group_id) VALUES(:citizen_id, :group_id)'
        , ['citizen_id' => $memberId, 'group_id' => $groupId])->rowCount();
        
        if($affectedRows === 0) {
            $this->throwPdoException();
        }
    }
    
    public function countGroups($typeId = null)
    {
        $whereClause =
            ($typeId !== null)
            ? "WHERE g.type_id = $typeId "
            : ''
        ;

        return $this->connection->query(
            "SELECT COUNT(*) as nb_groups FROM groups g $whereClause"
        )->fetch(\PDO::FETCH_ASSOC)['nb_groups'];
    }

    public function findRegionalGroups()
    {
        return $this->connection->query(
            'SELECT g.id, g.name, g.description, u.identity, g.created_at, g.updated_at, gt.label, r.Longitude, r.Latitude '.
            'FROM groups g '.
            'INNER JOIN group_types gt ON gt.id = g.type_id '.
            'INNER JOIN users u ON u.id = g.contact_id '.
            'INNER JOIN regions r ON r.Name = g.name '.
            'WHERE r.Longitude IS NOT NULL AND r.Latitude IS NOT NULL '.
            'ORDER BY g.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countGroupMembers($groupId)
    {
        return $this->connection->prepareStatement(
            'SELECT COUNT(*) AS count FROM citizen_groups WHERE group_id = :group_id', ['group_id' => $groupId])->fetch(\PDO::FETCH_ASSOC)['count'];
    }

    public function findGroupMembers($groupId)
    {
        $statement = $this->connection->prepareStatement(
            'SELECT u.id, u.identity, u.last_connected_at '.
            'FROM citizen_groups cg '.
            'INNER JOIN users u ON cg.citizen_id = u.id '.
            'WHERE cg.group_id = :group_id '.
            'ORDER BY u.identity ASC', ['group_id' => $groupId]);
        $members = [];
        while ($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $members[] =
                (new Member())
                ->setId($data['id'])
                ->setIdentity($data['identity'])
                ->setLastConnectedAt(new \DateTime($data['last_connected_at']))
            ;
        }

        return $members;
    }

    public function update(Group $group)
    {
        return $this->connection->prepareStatement(
            'UPDATE groups SET type_id = :type_id, description = :description, '.
            'name = :name, contact_id = :contact_id, updated_at = :updated_at WHERE id = :id', [
            'id' => $group->getId(),
            'type_id' => $group->getType()->getId(),
            'description' => $group->getDescription(),
            'name' => $group->getName(),
            'contact_id' => $group->getContact()->getId(),
            'updated_at' => (new \DateTime())->format('c'),
        ]);
    }

    /**
     * @param array $data
     *
     * @return \Wonderland\Application\Model\Group
     */
    protected function formatObject($data)
    {
        return
            (new Group())
            ->setId((int) $data['id'])
            ->setName($data['name'])
            ->setType(
                (new GroupType())
                ->setId((int) $data['type_id'])
                ->setLabel($data['label'])
            )
            ->setDescription($data['description'])
            ->setContact(
                (new Member())
                ->setId($data['contact_id'])
                ->setIdentity($data['identity'])
            )
            ->setIsPublic((bool) $data['is_public'])
            ->setCreatedAt((new \DateTime($data['created_at'])))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
    }
}
