<?php

namespace Wonderland\Application\Model;

class Group {
    /** @var int **/
    protected $id;
    /** @var \Wonderland\Application\Model\GroupType **/
    protected $type;
    /** @var string **/
    protected $name;
    /** @var string **/
    protected $description;
    /** @var \Wonderland\Application\Model\Member **/
    protected $contact;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \DateTime **/
    protected $updatedAt;
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Group
     */
    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param \Wonderland\Application\Model\GroupType $type
     * @return \Wonderland\Application\Model\Group
     */
    public function setType(GroupType $type) {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * @return \Wonderland\Application\Model\GroupType
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * @param string $name
     * @return \Wonderland\Application\Model\Group
     */
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * @param string $description
     * @return \Wonderland\Application\Model\Group
     */
    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $member
     * @return \Wonderland\Application\Model\Group
     */
    public function setContact(Member $member) {
        $this->contact = $member;
        
        return $this;
    }
    
    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getContact () {
        return $this->contact;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return \Wonderland\Application\Model\Group
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
     * @param \DateTime $updatedAt
     * @return \Wonderland\Application\Model\Group
     */
    public function setUpdatedAt(\DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
}