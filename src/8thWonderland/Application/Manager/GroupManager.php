<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Repository\GroupRepository;

class GroupManager
{
    /** @var \Wonderland\Application\Repository\GroupRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Application\Repository\GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->repository = $groupRepository;
    }

    /**
     * @param int $id
     *
     * @return Group|null
     */
    public function getGroup($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param int  $typeId
     * @param int  $minRange
     * @param int  $maxRange
     * @param bool $raw
     *
     * @return array
     */
    public function getGroups($typeId = null, $minRange = null, $maxRange = null, $raw = true)
    {
        return $this->repository->findGroups($typeId, $minRange, $maxRange, $raw);
    }

    /**
     * @param int $typeId
     *
     * @return int
     */
    public function countGroups($typeId = null)
    {
        return $this->repository->countGroups($typeId);
    }

    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param int                                  $groupId
     *
     * @return boolean
     */
    public function isMemberInGroup(Member $member, $groupId)
    {
        return isset($member->getGroups()[$groupId]);
    }
    
    /**
     * 
     * @param \Wonderland\Application\Model\Member $member
     * @param int $groupId
     */
    public function addMemberToGroup(Member $member, $groupId) {
        if($this->isMemberInGroup($member, $groupId)) {
            throw new BadRequestException('You are already in this group');
        }
        $this->repository->addMemberToGroup($member->getId(), $groupId);
    }

    /**
     * @return array
     */
    public function getRegionalGroups()
    {
        return $this->repository->findRegionalGroups();
    }

    /**
     * @param int $groupId
     *
     * @return int
     */
    public function countGroupMembers($groupId)
    {
        return $this->repository->countGroupMembers($groupId);
    }

    /**
     * @param int $groupId
     *
     * @return array
     */
    public function getGroupMembers($groupId)
    {
        return $this->repository->findGroupMembers($groupId);
    }

    /**
     * Update Group's contact.
     * 
     * @param \Wonderland\Application\Model\Group  $group
     * @param \Wonderland\Application\Model\Member $member
     *
     * @return int
     */
    public function updateContact(Group $group, Member $member)
    {
        $group->setContact($member);

        return $this->repository->update($group)->rowCount();
    }
}
