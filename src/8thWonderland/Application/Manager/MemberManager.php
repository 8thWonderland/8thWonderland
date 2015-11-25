<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

use Wonderland\Library\Translator;

use Wonderland\Library\Database\PdoDriver;
use Wonderland\Application\Repository\MemberRepository;

class MemberManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Application\Repository\MemberRepository **/
    protected $repository;
    /** @var \Wonderland\Application\Manager\CountryManager **/
    protected $countryManager;
    /** @var \Wonderland\Application\Manager\RegionManager **/
    protected $regionManager;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    
    /**
     * @param \Wonderland\Application\Repository\MemberRepository
     */
    public function __construct(
        MemberRepository $repository,
        PdoDriver $connection,
        CountryManager $countryManager,
        RegionManager $regionManager,
        Translator $translator
    ) {
        $this->connection = $connection;
        $this->repository = $repository;
        $this->countryManager = $countryManager;
        $this->regionManager = $regionManager;
        $this->translator = $translator;
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
     * Return errors array or a Member object
     * 
     * @param string $login
     * @param string $password
     * @param string $confirmationPassword
     * @param string $email
     * @param int $countryId
     * @param int $regionId
     * @return boolean | array
     */
    public function create($login, $password, $confirmationPassword, $email, $countryId, $regionId) {
        $errors = [];
        if(($country = $this->countryManager->get($countryId)) === null) {
            $errors[] = $this->translator->translate('registration.invalid_country');
        }
        if(($region = $this->regionManager->get($regionId)) === null) {
            $errors[] = $this->translator->translate('registration.invalid_region');
        }
        $errors = array_merge($errors, $this->validate($login, $password, $confirmationPassword, $email));
        
        if(count($errors) > 0) {
            return $errors;
        }
        
        try {
            return $this->repository->create(
                (new Member())
                ->setLogin($login)
                ->setPassword(hash('sha512', $password))
                ->setSalt(md5(uniqid().time()))
                ->setIdentity($login)
                ->setEmail($email)
                ->setCountry($country)
                ->setRegion($region)
                ->setLanguage($this->translator->getBrowserLang())
                ->setCreatedAt(new \DateTime())
                ->setLastConnectedAt(new \DateTime())
                ->setIsEnabled(true)
                ->setIsBanned(false)
            );
        } catch (\Exception $ex) {
            return [$ex->getMessage()];
        }
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     */
    public function update(Member $member) {
        $this->repository->update($member);
    }
    
    /**
     * @param string $login
     * @param string $password
     * @param string $confirmationPassword
     * @param string $email
     * @return array
     */
    public function validate($login, $password, $confirmationPassword, $email) {
        $errors = [];
        
        $member = $this->repository->getExistingLoginOrEmail($login, $email);
        if($member !== false) {
            if($member['login'] === $login && $member['identity'] === $login) {
                $errors[] = $this->translator->translate('registration.existing_login');
            }
            if($member['email'] === $email) {
                $errors[] = $this->translator->translate('registration.existing_mail');
            }
        }
        if($password !== $confirmationPassword) {
            $errors[] = $this->translator->translate('registration.password_mismatch');
        }
        if(!$this->validateIdentity($login)) {
            $errors[] = $this->translator->translate('registration.invalid_login');
        }
        if(!$this->validateEmailAddress($email)) {
            $errors[] = $this->translator->translate('registration.invalid_mail');
        }
        return $errors;
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
        return isset($member->getGroups()[$groupId]);
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