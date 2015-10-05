<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;

use Wonderland\Library\Database\PdoDriver;

class MemberManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver
     */
    public function __construct(PdoDriver $connection) {
        $this->connection = $connection;
    }
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Member
     */
    public function getMember($id) {
        return $this->formatMemberData($this->connection->prepareStatement(
            'SELECT u.id, u.login, u.password, u.salt, u.identity, u.gender,' .
            'u.email, u.avatar, u.language, u.country, u.region, u.last_connected_at, ' .
            'u.created_at, u.is_enabled, u.is_banned, u.theme, ' .
            'g.id AS group_id, g.name, g.description, g.created_at AS group_created_at, ' .
            'g.updated_at AS group_updated_at, gt.id AS group_type_id, gt.label, ' .
            'u2.id AS contact_id, u2.identity AS contact_identity ' .
            'FROM users u ' .
            'LEFT JOIN citizen_groups cg ON citizen_id = u.id ' .
            'LEFT JOIN groups g ON g.id = cg.group_id OR g.contact_id = u.id ' .
            'LEFT JOIN group_types gt ON gt.id = g.type_id ' .
            'LEFT JOIN users u2 ON u2.id = g.contact_id ' .
            "WHERE u.id = :id"
        , ['id' => $id]));
    }
    
    /**
     * @param string $identity
     * @return \Wonderland\Application\Model\Member
     */
    public function getMemberByIdentity($identity) {
        return $this->formatMemberData($this->connection->prepareStatement(
            'SELECT u.id, u.login, u.password, u.salt, u.identity, u.gender,' .
            'u.email, u.avatar, u.language, u.country, u.region, u.last_connected_at, ' .
            'u.created_at, u.is_enabled, u.is_banned, u.theme, ' .
            'g.id AS group_id, g.name, g.description, g.created_at AS group_created_at, ' .
            'g.updated_at AS group_updated_at, gt.id AS group_type_id, gt.label, ' .
            'u2.id AS contact_id, u2.identity AS contact_identity ' .
            'FROM users u ' .
            'LEFT JOIN citizen_groups cg ON citizen_id = u.id ' .
            'LEFT JOIN groups g ON g.id = cg.group_id OR g.contact_id = u.id ' .
            'LEFT JOIN group_types gt ON gt.id = g.type_id ' .
            'LEFT JOIN users u2 ON u2.id = g.contact_id ' .
            "WHERE u.identity = :identity"
        , ['identity' => $identity]));
    }
    
    /**
     * @param string $login
     * @param string $password
     * @return \Wonderland\Application\Model\Member
     */
    public function getMemberByLoginAndPassword($login, $password) {
        return $this->formatMemberData($this->connection->prepareStatement(
            'SELECT u.id, u.login, u.password, u.salt, u.identity, u.gender,' .
            'u.email, u.avatar, u.language, u.country, u.region, u.last_connected_at, ' .
            'u.created_at, u.is_enabled, u.is_banned, u.theme, ' .
            'g.id AS group_id, g.name, g.description, g.created_at AS group_created_at, ' .
            'g.updated_at AS group_updated_at, gt.id AS group_type_id, gt.label, ' .
            'u2.id AS contact_id, u2.identity AS contact_identity ' .
            'FROM users u ' .
            'LEFT JOIN citizen_groups cg ON citizen_id = u.id ' .
            'LEFT JOIN groups g ON g.id = cg.group_id OR g.contact_id = u.id ' .
            'LEFT JOIN group_types gt ON gt.id = g.type_id ' .
            'LEFT JOIN users u2 ON u2.id = g.contact_id ' .
            'WHERE u.login = :login AND u.password = :password'
        , ['login' => $login, 'password' => $password]));
    }
    
    /**
     * Turn fetched data into Member object
     * 
     * @param array $rawData
     * @return \Wonderland\Application\Model\Member
     */
    public function formatMemberData($rawData) {
        if(($data = $rawData->fetch()) === false) {
            return null;
        }
        $member =
            (new Member())
            ->setId($data['id'])
            ->setLogin($data['login'])
            ->setIdentity($data['identity'])
            ->setPassword($data['password'])
            ->setEmail($data['email'])
            ->setAvatar($data['avatar'])
            ->setGender($data['gender'])
            ->setLanguage($data['language'])
            ->setCountry($data['country'])
            ->setRegion($data['region'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setLastConnectedAt(new \DateTime($data['last_connected_at']))
            ->setIsEnabled($data['is_enabled'])
            ->setIsBanned($data['is_banned'])
            ->setTheme($data['theme'])
        ;
        // Currently, the next rows are containing the user's groups
        while($data = $rawData->fetch()) {
            $member->addGroup(
                (new Group())
                ->setId($data['group_id'])
                ->setName($data['name'])
                ->setDescription($data['description'])
                ->setType(
                    (new GroupType())
                    ->setId($data['group_type_id'])
                    ->setLabel($data['label'])
                )
                ->setContact(
                    (new Member())
                    ->setId($data['contact_id'])
                    ->setIdentity($data['contact_identity'])
                )
                ->setCreatedAt(new \DateTime($data['group_created_at']))
                ->setUpdatedAt(new \DateTime($data['group_updated_at']))
            );
        }
        return $member;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    public function createMember(Member $member) {
        $this->connection->prepareStatement(
            'INSERT INTO users(login, password, salt, identity, gender, ' .
            'email, avatar, language, country, region, last_connected_at, created_at, is_enabled, is_banned, theme) ' .
            "VALUES(login = :login, password = :password, salt = :salt, ' .
            'identity = :identity, gender = :gender, email = :email, avatar = :avatar, ' .
            'language = :language, country = :country, region = :region, ' .
            'last_connected_at = :last_connected_at, created_at = :created_at, ' .
            'is_enabled = :is_enabled, is_banned = :is_banned, theme = :theme)"
        , [
            'login' => $member->getLogin(),
            'password' => $member->getPassword(),
            'salt' => $member->getSalt(),
            'identity' => $member->getIdentity(),
            'gender' => $member->getGender(),
            'email' => $member->getEmail(),
            'avatar' => $member->getAvatar(),
            'country' => $member->getCountry(),
            'region' => $member->getRegion(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => $member->getIsEnabled(),
            'is_banned' => $member->getIsBanned(),
            'theme' => $member->getTheme(),
            'id' => $member->getId()
        ]);
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    public function updateMember(Member $member) {
        $this->connection->prepareStatement(
            'UPDATE users SET login = :login, password = :password, salt = :salt, ' .
            'identity = :identity, gender = :gender, email = :email, avatar = :avatar, ' .
            'language = :language, country = :country, region = :region, ' .
            'last_connected_at = :last_connected_at, created_at = :created_at, ' .
            'is_enabled = :is_enabled, is_banned = :is_banned, theme = :theme WHERE id = :id'
        , [
            'login' => $member->getLogin(),
            'password' => $member->getPassword(),
            'salt' => $member->getSalt(),
            'identity' => $member->getIdentity(),
            'gender' => $member->getGender(),
            'email' => $member->getEmail(),
            'avatar' => $member->getAvatar(),
            'country' => $member->getCountry(),
            'region' => $member->getRegion(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => $member->getIsEnabled(),
            'is_banned' => $member->getIsBanned(),
            'theme' => $member->getTheme(),
            'id' => $member->getId()
        ]);
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
        return $this->connection->query('SELECT COUNT(*) AS count FROM users')->fetch()['count'];
    }
    
    /**
     * @return int
     */
    public function countActiveMembers() {
        return $this->connection->query(
            'SELECT COUNT(*) AS count FROM users WHERE DATEDIFF(CURDATE(), last_connected_at) < 21'
        )->fetch()['count'];
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
        return $this->connection->query("SELECT Code, '$language' FROM country")->fetchAll();
    }
    
    /**
     * @param array $params
     * @return array
     */
    public function getMembers($params) {
        $search = '';
        $table = 'users u';
        if (!empty($params['sel_groups'])) {
            $table = 'citizen_groups, users u';
            $search = " WHERE citizen_id = id AND group_id = {$params['sel_groups']} ";
        }
        
        return $this->connection->prepareStatement(
            'SELECT u.id, avatar, identity, gender, email, language, country, region, last_connected_at, u.created_at ' .
            'FROM :table :search ORDER BY identity ASC'
        , ['table' => $table, 'search' => $search])->fetchAll();
    }
    
    public function findByGroup($groupId) {
        $statement = $this->connection->prepareStatement(
            'SELECT u.id, u.identity, u.last_connected_at ' .
            'FROM citizen_groups cg ' .
            'INNER JOIN users u ON cg.citizen_id = u.id ' .
            'WHERE cg.group_id = :group_id ' .
            'ORDER BY u.identity ASC'
        , ['group_id' => $groupId]);
        $members = [];
        while($data = $statement->fetch()) {
            $members[] =
                (new Member())
                ->setId($data['id'])
                ->setIdentity($data['identity'])
                ->setLastConnectedAt(new \DateTime($data['last_connected_at']))
            ;
        }
        return $members;
    }
    
    /**
     * @return array
     */
    public function getContactGroups() {
        return $this->connection->query(
            'SELECT u.id, g.name, u.identity ' .
            'FROM users u ' .
            'INNER JOIN groups g ON g.contact_id = u.id ' .
            'ORDER BY g.name ASC'
        )->fetchAll();
    }
}