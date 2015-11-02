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
    
    public function getGroup($id) {
        if(($data = $this->repository->find($id)) === false) {
            return null;
        }
        return
            (new Group())
            ->setId($id)
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setType((new GroupType())->setLabel($data['label']))
            ->setContact((new Member())->setId($data['contact_id']))
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setUpdatedAt(new \DateTime($data['updated_at']))
        ;
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
                ->setType((new GroupType())->setLabel($data['label']))
                ->setDescription($data['description'])
                ->setContact((new Member())->setIdentity($data['identity']))
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
        return $this->repository->findRegionalGroups();
    }
    
    /**
     * @param int $groupId
     * @return int
     */
    public function countMembers($groupId) {
        return $this->repository->countGroupMembers($groupId);
    }
    
    /**
     * @param int $groupId
     * @return array
     */
    public function getGroupMembers($groupId) {
        return $this->repository->findGroupMembers($groupId);
    }

    /**
     * Update Group's contact
     * 
     * @param \Wonderland\Application\Model\Group $group
     * @param \Wonderland\Application\Model\Member $member
     * @return int
     */
    public function updateContact(Group $group, Member $member) {
        $group->setContact($member);
        return $this->repository->update($group)->rowCount();
    }
}