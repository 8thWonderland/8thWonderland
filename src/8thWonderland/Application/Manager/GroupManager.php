<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;
use Wonderland\Application\Model\Member;

use Wonderland\Application\Repository\GroupRepository;

class GroupManager {
    /** @var \Wonderland\Application\Repository\GroupRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Application\Repository\GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository) {
        $this->repository = $groupRepository;
    }
    
    /**
     * @return array
     */
    public function getGroups() {
        $rows = $this->repository->findGroups();
        $nbRows = count($rows);
        
        $groups = [];
        for($i = 0; $i < $nbRows; ++$i) {
            $data = $rows[$i];
            
            $groups[] =
                (new Group())
                ->setId($data['id'])
                ->setName($data['name'])
                ->setType(
                    (new GroupType())
                    ->setLabel($data['label'])
                )
                ->setDescription($data['description'])
                ->setContact(
                    (new Member())
                    ->setIdentity($data['identity'])
                )
                ->setCreatedAt((new \DateTime($data['created_at'])))
                ->setUpdatedAt(new \DateTime($data['updated_at']))
            ;
        }
        return $groups;
    }
    
    /**
     * @return array
     */
    public function getRegionalGroups() {
        $rows = $this->repository->findRegionalGroups();
    }
    
    /**
     * @param int $groupId
     * @return int
     */
    public function countMembers($groupId) {
        return $this->repository->countGroupMembers($groupId);
    }

    /**
     * Update Group's contact
     * 
     * @param int $groupId
     * @param int $contactId
     * @return int
     */
    public function updateContact($groupId, $contactId) {
        $this->connection->query("UPDATE groups SET contact_id = $contactId WHERE id = $groupId");
        return $this->connection->affected_rows;
    }
}