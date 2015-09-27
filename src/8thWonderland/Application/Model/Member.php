<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Auth;

class Member {
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
    protected $gender;
    /** @var string **/
    protected $email;
    /** @var string **/
    protected $avatar;
    /** @var string **/
    protected $country;
    /** @var string **/
    protected $region;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \DateTime **/
    protected $lastConnectedAt;
    /** @var boolean **/
    protected $isEnabled;
    /** @var boolean **/
    protected $isBanned;
    /** @var string **/
    protected $theme;
    
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param string $login
     * @return \Wonderland\Application\Model\Member
     */
    public function setLogin($login) {
        $this->login = $login;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }
    
    /**
     * @param string $identity
     * @return \Wonderland\Application\Model\Member
     */
    public function setIdentity($identity) {
        $this->identity = $identity;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getIdentity() {
        return $this->identity;
    }
    
    /**
     * @param string $language
     * @return \Wonderland\Application\Model\Member
     */
    public function setLanguage($language) {
        $this->language = $language;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }
    
    /**
     * @param string $password
     * @return \Wonderland\Application\Model\Member
     */
    public function setPassword($password) {
        $this->password = $password;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
    
    /**
     * @param string $salt
     * @return \Wonderland\Application\Model\Member
     */
    public function setSalt($salt) {
        $this->salt = $salt;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSalt() {
        return $this->salt;
    }
    
    /**
     * @param string $gender
     * @return \Wonderland\Application\Model\Member
     */
    public function setGender($gender) {
        $this->gender = $gender;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }
    
    /**
     * @param string $email
     * @return \Wonderland\Application\Model\Member
     */
    public function setEmail($email) {
        $this->email = $email;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * @param string $avatar
     * @return \Wonderland\Application\Model\Member
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }
    
    /**
     * @param string $country
     * @return \Wonderland\Application\Model\Member
     */
    public function setCountry($country) {
        $this->country = $country;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }
    
    /**
     * @param string $region
     * @return \Wonderland\Application\Model\Member
     */
    public function setRegion($region) {
        $this->region = $region;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return \Wonderland\Application\Model\Member
     */
    public function setCreatedAt(\DateTime $createdAt) {
        $this->createdAt = $createdAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    /**
     * @param \DateTime $lastConnectedAt
     * @return \Wonderland\Application\Model\Member
     */
    public function setLastConnectedAt(\DateTime $lastConnectedAt) {
        $this->lastConnectedAt = $lastConnectedAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getLastConnectedAt() {
        return $this->lastConnectedAt;
    }
    
    /**
     * @param boolean $isEnabled
     * @return \Wonderland\Application\Model\Member
     */
    public function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsEnabled() {
        return $this->isEnabled;
    }
    
    /**
     * @param boolean $isBanned
     * @return \Wonderland\Application\Model\Member
     */
    public function setIsBanned($isBanned) {
        $this->isBanned = $isBanned;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsBanned() {
        return $this->isBanned;
    }
    
    /**
     * @param string $theme
     * @return \Wonderland\Application\Model\Member
     */
    public function setTheme($theme) {
        $this->theme = $theme;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTheme() {
        return $this->theme;
    }
}