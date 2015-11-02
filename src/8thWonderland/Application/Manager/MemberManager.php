<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Database\PdoDriver;
use Wonderland\Application\Repository\MemberRepository;

class MemberManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Application\Repository\MemberRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Application\Repository\MemberRepository
     */
    public function __construct(MemberRepository $repository, PdoDriver $connection) {
        $this->connection = $connection;
        $this->repository = $repository;
    }
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Member
     */
    public function getMember($id) {
        return $this->repository->findOneBy(['id' => $id]);
    }
    
    /**
     * @param string $identity
     * @return \Wonderland\Application\Model\Member
     */
    public function getMemberByIdentity($identity) {
        return $this->repository->findOneBy(['identity' => $identity]);
    }
    
    /**
     * @param string $login
     * @param string $password
     * @return \Wonderland\Application\Model\Member
     */
    public function getMemberByLoginAndPassword($login, $password) {
        return $this->repository->findOneBy(['login' => $login, 'password' => $password]);
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    public function create(Member $member) {
        return $this->repository->create($member);
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    public function update(Member $member) {
        $this->repository->update($member);
    }
    
    /**
     * @param string $identity
     * @return boolean
     */
    public function validateIdentity($identity) {
        $res = '';
        preg_match("/^[a-zA-Z][a-zA-Z0-9 _-]+/", $identity, $res);

        if (
            !$res || $res[0] !== $identity ||
            intval($identity) !== 0 ||
            strlen($identity) < 3
        ) {
            return false;
        }
        return true;
    }
    
    /**
     * @param string $email
     * @return boolean
     */
    public function validateEmailAddress($email) {
        $res = '';
        $MXHost = '';
        preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email, $res);
        
        if (!$res || $res[0] !== $email) {
            return false;
        }
        $emailParts = explode('@',$email);
        if(!getmxrr($emailParts[1], $MXHost)) {
            return false;
        }
        return true;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param int $groupId
     * @return int
     */
    public function isMemberInGroup(Member $member, $groupId) {
        return $this->connection->prepareStatement(
            'SELECT COUNT(*) FROM citizen_groups WHERE citizen_id = :id AND group_id = :group_id'
        , ['id' => $member->getId(), 'group_id' => $groupId]);
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param int $groupId
     * @return int
     */
    public function isContact(Member $member, $groupId = null) {
        return
            (!isset($groupId))
            ? $this->connection->prepareStatement(
                'SELECT COUNT(*) FROM groups WHERE contact_id = :id'
            , ['id' => $member->getId()])
            : $this->connection->prepareStatement(
                'SELECT COUNT(*) FROM groups WHERE contact_id = :id AND id = :group_id'
            , ['id' => $member->getId(), 'group_id' => $groupId])
        ;
    }

    /**
     * @return int
     */
    public function countMembers() {
        return $this->repository->countMembers();
    }
    
    /**
     * @return int
     */
    public function countActiveMembers() {
        return $this->connection->query(
            'SELECT COUNT(*) AS count FROM users WHERE DATEDIFF(CURDATE(), last_connected_at) < 21'
        )->fetch(\PDO::FETCH_ASSOC)['count'];
    }
    
    /**
     * Countries should have their own manager
     * 
     * @ToRemove
     * @param string $language
     * @return array
     */
    public function getCountries($language) {
        if ($this->connection->columnExists($language, 'country') === false) {
            $language = 'en';
        }
        return $this->connection->query("SELECT Code, $language FROM country");
    }
    
    /**
     * @param array $params
     * @return array
     */
    public function getMembers($params) {
        $search = '';
        $table = 'users u';
        if (!empty($params['sel_groups'])) {
            $table = 'citizen_groups cg, users u';
            $search = " WHERE cg.citizen_id = u.id AND cg.group_id = {$params['sel_groups']} ";
        }
        return $this->connection->query(
            'SELECT u.id, u.avatar, u.identity, u.gender, u.email, u.language, u.country, u.region, u.last_connected_at, u.created_at ' .
            "FROM $table $search ORDER BY u.identity ASC"
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @return array
     */
    public function getContactGroups() {
        return $this->repository->getContactGroups();
    }
}