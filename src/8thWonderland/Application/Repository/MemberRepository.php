<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;

class MemberRepository extends AbstractRepository {
    /**
     * @param Member $member
     * @return boolean|array
     */
    public function create(Member $member) {
        $statement = $this->connection->prepareStatement(
            'INSERT INTO users(login, password, salt, identity, ' .
            'email, language, country_id, region_id, last_connected_at, created_at, is_enabled, is_banned) ' .
            'VALUES(:login, :password, :salt, :identity, :email, ' .
            ':language, :country_id, :region_id, :last_connected_at, :created_at, ' .
            ':is_enabled, :is_banned)'
        , [
            'login' => $member->getLogin(),
            'password' => $member->getPassword(),
            'salt' => $member->getSalt(),
            'identity' => $member->getIdentity(),
            'email' => $member->getEmail(),
            'language' => $member->getLanguage(),
            'country_id' => $member->getCountry()->getId(),
            'region_id' => $member->getRegion()->getId(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => (int) $member->getIsEnabled(),
            'is_banned' => (int) $member->getIsBanned()
        ]);
        if($statement->rowCount() === 0) {
            return $statement->errorInfo();
        }
        return true;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @return boolean|array
     */
    public function update(Member $member) {
        $statement = $this->connection->prepareStatement(
            'UPDATE users SET login = :login, password = :password, salt = :salt, ' .
            'identity = :identity, email = :email, avatar = :avatar, ' .
            'language = :language, country_id = :country_id, region_id = :region_id, ' .
            'last_connected_at = :last_connected_at, created_at = :created_at, ' .
            'is_enabled = :is_enabled, is_banned = :is_banned WHERE id = :id'
        , [
            'login' => $member->getLogin(),
            'password' => $member->getPassword(),
            'salt' => $member->getSalt(),
            'identity' => $member->getIdentity(),
            'email' => $member->getEmail(),
            'avatar' => $member->getAvatar(),
            'language' => $member->getLanguage(),
            'country_id' => $member->getCountry()->getId(),
            'region_id' => $member->getRegion()->getId(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => (int) $member->getIsEnabled(),
            'is_banned' => (int) $member->getIsBanned(),
            'id' => $member->getId()
        ]);
        if($statement->rowCount() === 0) {
            return $statement->errorInfo();
        }
        return true;
    }
     
    /**
     * @param string $login
     * @param string $email
     * @return array
     */
    public function getExistingLoginOrEmail($login, $email) {
        return $this->connection->prepareStatement(
            'SELECT login, identity, email FROM users WHERE login = :login OR identity = :login OR email = :email'
        , ['login' => $login, 'email' => $email])->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function findOneBy($criterias) {
        $whereClause = 
            (count($criterias) > 0)
            ? 'WHERE '
            : ''
        ;
        $arguments = [];
        foreach($criterias as $column => $value) {
            $whereClause .= "u.$column = :$column AND ";
            $arguments[":$column"] = $value;
        }
        
        return $this->formatMemberData($this->connection->prepareStatement(
            'SELECT u.id, u.login, u.password, u.salt, u.identity,' .
            'u.email, u.avatar, u.language, u.last_connected_at, ' .
            'u.created_at, u.is_enabled, u.is_banned, ' .
            'c.id as country_id, c.code, c.label, ' .
            'g.id AS group_id, g.name, g.description, g.created_at AS group_created_at, ' .
            'g.updated_at AS group_updated_at, gt.id AS group_type_id, gt.label, ' .
            'u2.id AS contact_id, u2.identity AS contact_identity ' .
            'FROM users u ' .
            'LEFT JOIN countries c ON c.id = u.country_id ' .
            'LEFT JOIN regions r ON r.id = u.region_id ' .
            'LEFT JOIN citizen_groups cg ON citizen_id = u.id ' .
            'LEFT JOIN groups g ON g.id = cg.group_id OR g.contact_id = u.id ' .
            'LEFT JOIN group_types gt ON gt.id = g.type_id ' .
            'LEFT JOIN users u2 ON u2.id = g.contact_id ' .
            // substr remove the last "AND " string
            substr($whereClause, 0, -4) .
            'GROUP BY g.id'
        , $arguments));
    }
    
    /**
     * Turn raw data into Member object
     * 
     * @param array $rawData
     * @return \Wonderland\Application\Model\Member
     */
    public function formatMemberData($rawData) {
        if(($data = $rawData->fetch(\PDO::FETCH_ASSOC)) === false) {
            return null;
        }
        $member =
            (new Member())
            ->setId((int) $data['id'])
            ->setLogin($data['login'])
            ->setIdentity($data['identity'])
            ->setPassword($data['password'])
            ->setEmail($data['email'])
            ->setAvatar($data['avatar'])
            ->setLanguage($data['language'])
            ->setCountry((int) $data['country_id'])
            ->setRegion((int) $data['region_id'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setLastConnectedAt(new \DateTime($data['last_connected_at']))
            ->setIsEnabled((bool) $data['is_enabled'])
            ->setIsBanned((bool) $data['is_banned'])
        ;
        if(isset($data['group_id'])) {
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
        // Currently, the next rows are containing the user's groups
        while($data = $rawData->fetch(\PDO::FETCH_ASSOC)) {
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
     * @return array
     */
    public function getContactGroups() {
        return $this->connection->query(
            'SELECT u.id, g.name, u.identity ' .
            'FROM users u ' .
            'INNER JOIN groups g ON g.contact_id = u.id ' .
            'ORDER BY g.name ASC'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @return int
     */
    public function countMembers() {
        return $this->connection->query('SELECT COUNT(*) AS count FROM users')->fetch(\PDO::FETCH_ASSOC)['count'];
    }
}