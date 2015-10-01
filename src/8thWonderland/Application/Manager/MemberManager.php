<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Application;

class MemberManager {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Member
     */
    public function getMember($id) {
        $data = $this->application->get('mysqli')->select(
            'SELECT id, login, password, salt, identity, gender, email, avatar, language, ' .
            "country, region, last_connected_at, created_at, is_enabled, is_banned, theme FROM users WHERE id = $id"
        );
        return $this->formatMemberData($data[0]);
    }
    
    /**
     * @param string $login
     * @param string $password
     * @return \Wonderland\Application\Model\Member
     */
    public function getMemberByLoginAndPassword($login, $password) {
        $data = $this->application->get('mysqli')->select(
            'SELECT id, login, password, salt, identity, gender, email, avatar, language, ' .
            "country, region, last_connected_at, created_at, is_enabled, is_banned, theme FROM users WHERE login = '$login' AND password = '$password'"
        );
        return $this->formatMemberData($data[0]);
    }
    
    /**
     * Turn fetched data into Member object
     * 
     * @param array $data
     * @return \Wonderland\Application\Model\Member
     */
    public function formatMemberData($data) {
        return
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
    }
    
    public function createMember(Member $member) {
        $this->application->get('mysqli')->query(
            'INSERT INTO users(login, password, salt, identity, gender, ' .
            'email, avatar, language, country, region, last_connected_at, created_at, is_enabled, is_banned, theme) ' .
            "VALUES('{$member->getLogin()}', '{$member->getPassword()}', '{$member->getSalt()}', " .
            "'{$member->getIdentity()}', '{$member->getGender()}', '{$member->getEmail()}', " .
            "'{$member->getAvatar()}', '{$member->getLanguage()}', '{$member->getCountry()}', " .
            "'{$member->getRegion()}', '{$member->getLastConnectedAt()->format('c')}', " .
            "'{$member->getCreatedAt()->format('c')}', {$member->getIsEnabled()}, " .
            "{$member->getIsBanned()}, '{$member->getTheme()}')"
        );
    }
    
    public function updateMember(Member $member) {
        $this->application->get('mysqli')->query(
            "UPDATE users SET login = '{$member->getLogin()}', password = '{$member->getPassword()}', " .
            "salt = '{$member->getSalt()}', identity = '{$member->getIdentity()}', " .
            "gender = '{$member->getGender()}', email = '{$member->getEmail()}', " .
            "avatar = '{$member->getAvatar()}', language = '{$member->getLanguage()}', " .
            "country = '{$member->getCountry()}', region = '{$member->getRegion()}', " .
            "last_connected_at = '{$member->getLastConnectedAt()->format('c')}', created_at = '{$member->getCreatedAt()->format('c')}', " .
            "is_enabled = {$member->getIsEnabled()}, is_banned = {$member->getIsBanned()}, " .
            "theme = '{$member->getTheme()}' WHERE id = {$member->getId()}"
        );
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
     * @param int $userId
     * @param int $groupId
     * @return int
     */
    public function isMemberInGroup($userId, $groupId) {
        return $this
            ->application
            ->get('mysqli')
            ->count('Citizen_Groups', sprintf('WHERE Citizen_id=%d AND Group_id=%d', $userId, $groupId))
        ;
    }
    
    /**
     * @param int $groupId
     * @return int
     */
    public function isContact($groupId = null) {
        $db = $this->application->get('mysqli');
        $userId = $this->application->get('session')->get('__id__');
        
        return
            (!isset($groupId))
            ? $db->count('Groups', " WHERE ID_Contact = $userId")
            : $db->count('Groups', " WHERE ID_Contact = $userId AND Group_id = $groupId")
        ;
    }

    /**
     * @return int
     */
    public static function countMembers() {
        return $this->application->get('mysqli')->count('users');
    }
    
    /**
     * @return int
     */
    public static function countActiveMembers() {
        return $this->application->get('mysqli')->count('users', ' WHERE DATEDIFF(CURDATE(), last_connected_at) < 21');
    }
    
    /**
     * Countries should have their own manager
     * 
     * @ToRemove
     * @param string $language
     * @return array
     */
    public function getCountries($language) {
        $db = $this->application->get('mysqli');
        if ($db->columnExists($language, 'country') === 0) {
            $language = 'en';
        }
        return $db->select("SELECT Code, '$language' FROM country");
    }
    
    /**
     * @param array $params
     * @return array
     */
    public function getMembers($params) {
        $search = '';
        $table = 'users';
        if (!empty($params['sel_groups'])) {
            $table = 'Citizen_Groups, users';
            $search = " WHERE Citizen_id = id AND Group_id={$params['sel_groups']} ";
        }
        
        return $this->application->get('mysqli')->select(
            'SELECT id, avatar, identity, gender, email, language, country, region, last_connected_at, created_at ' .
            "FROM $table $search ORDER BY identity ASC"
        );
    }
    
    /**
     * @return array
     */
    public function getContactGroups() {
        return $this->application->get('mysqli')->select(
            'SELECT id, Group_name, identity ' .
            'FROM users, Groups WHERE id = ID_Contact ' .
            'ORDER BY Group_name ASC'
        );
    }
}