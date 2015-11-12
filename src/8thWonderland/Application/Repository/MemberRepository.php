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
            'INSERT INTO users(login, password, salt, identity, gender, ' .
            'email, avatar, language, country, region, last_connected_at, created_at, is_enabled, is_banned, theme) ' .
            'VALUES(:login, :password, :salt, :identity, :gender, :email, :avatar, ' .
            ':language, :country, :region, :last_connected_at, :created_at, ' .
            ':is_enabled, :is_banned, :theme)'
        , [
            'login' => $member->getLogin(),
            'password' => $member->getPassword(),
            'salt' => $member->getSalt(),
            'identity' => $member->getIdentity(),
            'gender' => $member->getGender(),
            'email' => $member->getEmail(),
            'avatar' => $member->getAvatar(),
            'language' => $member->getLanguage(),
            'country' => $member->getCountry(),
            'region' => $member->getRegion(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => (int) $member->getIsEnabled(),
            'is_banned' => (int) $member->getIsBanned(),
            'theme' => $member->getTheme()
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
            'language' => $member->getLanguage(),
            'country' => $member->getCountry(),
            'region' => $member->getRegion(),
            'last_connected_at' => $member->getLastConnectedAt()->format('c'),
            'created_at' => $member->getCreatedAt()->format('c'),
            'is_enabled' => (int) $member->getIsEnabled(),
            'is_banned' => (int) $member->getIsBanned(),
            'theme' => $member->getTheme(),
            'id' => $member->getId()
        ]);
        if($statement->rowCount() === 0) {
            return $statement->errorInfo();
        }
        return true;
    }
    
    /**
     * @param array $criterias
     * @param boolean $raw
     * @return \Wonderland\Application\Model\Member|array
     */
    public function findOneBy($criterias, $raw = false) {
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
        
        $statement = $this->connection->prepareStatement(
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
            // substr remove the last "AND " string
            substr($whereClause, 0, -4) .
            'GROUP BY g.id'
        , $arguments);
        return
            ($raw)
            ? $this->formatArray($statement)
            : $this->formatObject($statement)
        ;
    }
    
    /**
     * Turn statement containing one member into Member object
     * 
     * @param \PDOStatement $statement
     * @return \Wonderland\Application\Model\Member
     */
    public function formatObject($statement) {
        if(($data = $statement->fetch(\PDO::FETCH_ASSOC)) === false) {
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
            ->setGender($data['gender'])
            ->setLanguage($data['language'])
            ->setCountry((int) $data['country'])
            ->setRegion((int) $data['region'])
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setLastConnectedAt(new \DateTime($data['last_connected_at']))
            ->setIsEnabled((bool) $data['is_enabled'])
            ->setIsBanned((bool) $data['is_banned'])
            ->setTheme($data['theme'])
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
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
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
     * Turn statement containing one member into Member data array
     * 
     * @param \PDOStatement $statement
     * @return array
     */
    public function formatArray($statement) {
        if(($data = $statement->fetch(\PDO::FETCH_ASSOC)) === false) {
            return null;
        }
        $member = [
            'id' => $data['id'],
            'login' => $data['login'],
            'identity' => $data['identity'],
            'password' => $data['password'],
            'email' => $data['email'],
            'avatar' => $data['avatar'],
            'gender' => $data['gender'],
            'language' => $data['language'],
            'country' => (int) $data['country'],
            'region' => (int) $data['region'],
            'created_at' => $data['created_at'],
            'last_connected_at' => $data['last_connected_at'],
            'is_enabled' => (bool) $data['is_enabled'],
            'is_banned' => (bool) $data['is_banned'],
            'theme' => $data['theme']
        ];
        if(isset($data['group_id'])) {
            $member['groups'][] = [
                'id' => $data['group_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'type' => [
                    'id' => $data['group_type_id'],
                    'label' => $data['label'],
                ],
                'contact' => [
                    'id' => $data['contact_id'],
                    'identity' => $data['contact_identity']
                ],
                'created_at' => $data['group_created_at'],
                'updated_at' => $data['group_updated_at']
            ];
        }
        // Currently, the next rows are containing the user's groups
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $member['groups'][] = [
                'id' => $data['group_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'type' => [
                    'id' => $data['group_type_id'],
                    'label' => $data['label'],
                ],
                'contact' => [
                    'id' => $data['contact_id'],
                    'identity' => $data['contact_identity']
                ],
                'created_at' => $data['group_created_at'],
                'updated_at' => $data['group_updated_at']
            ];
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