<?php

namespace Wonderland\Application\Model;

class Member
{
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $login;
    /** @var string **/
    protected $identity;
    /** @var string **/
    protected $language;
    /** @var string **/
    protected $password;
    /** @var string **/
    protected $salt;
    /** @var string **/
    protected $email;
    /** @var string **/
    protected $avatar;
    /** @var Country **/
    protected $country;
    /** @var Region **/
    protected $region;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \DateTime **/
    protected $lastConnectedAt;
    /** @var bool **/
    protected $isEnabled;
    /** @var bool **/
    protected $isBanned;
    /** @var array **/
    protected $groups;

    public function __construct()
    {
        $this->groups = [];
    }

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $login
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $identity
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param string $language
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $password
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $salt
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $email
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $avatar
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param \Wonderland\Application\Model\Country $country
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param \Wonderland\Application\Model\Region $region
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $lastConnectedAt
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setLastConnectedAt(\DateTime $lastConnectedAt)
    {
        $this->lastConnectedAt = $lastConnectedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastConnectedAt()
    {
        return $this->lastConnectedAt;
    }

    /**
     * @param bool $isEnabled
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isBanned
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function setIsBanned($isBanned)
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * @param \Wonderland\Application\Model\Group $group
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function addGroup(Group $group)
    {
        $this->groups[$group->getId()] = $group;

        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return \Wonderland\Application\Model\Member
     */
    public function removeGroup($groupId)
    {
        unset($this->groups[$groupId]);

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
