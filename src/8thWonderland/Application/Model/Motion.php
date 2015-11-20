<?php

namespace Wonderland\Application\Model;

class Motion {
    /** @var int **/
    protected $id;
    /** @var \Wonderland\Application\Model\MotionTheme **/
    protected $theme;
    /**@var string **/
    protected $title;
    /** @var string **/
    protected $description;
    /** @var string **/
    protected $means;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \DateTime **/
    protected $endedAt;
    /** @var \Wonderland\Application\Model\Member **/
    protected $author;
    /** @var boolean **/
    protected $isActive;
    /** @var boolean **/
    protected $isApproved;
    /** @var int **/
    protected $score;
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Motion
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
     * @param \Wonderland\Application\Model\MotionTheme $theme
     * @return \Wonderland\Application\Model\Motion
     */
    public function setTheme(MotionTheme $theme) {
        $this->theme = $theme;
        
        return $this;
    }
    
    /**
     * @return \Wonderland\Application\Model\MotionTheme
     */
    public function getTheme() {
        return $this->theme;
    }
    
    /**
     * @param string $title
     * @return \Wonderland\Application\Model\Motion
     */
    public function setTitle($title) {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @return strin
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * @param string $description
     * @return \Wonderland\Application\Model\Motion
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
     * @param string $means
     * @return \Wonderland\Application\Model\Motion
     */
    public function setMeans($means) {
        $this->means = $means;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMeans() {
        return $this->means;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return \Wonderland\Application\Model\Motion
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
     * @param \DateTime $endedAt
     * @return \Wonderland\Application\Model\Motion
     */
    public function setEndedAt(\DateTime $endedAt) {
        $this->endedAt = $endedAt;
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getEndedAt() {
        return $this->endedAt;
    }
    
    /**
     * @param string $author
     * @return \Wonderland\Application\Model\Motion
     */
    public function setAuthor($author) {
        $this->author = $author;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }
    
    /**
     * @param boolean $isActive
     * @return \Wonderland\Application\Model\Motion
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsActive() {
        return $this->isActive;
    }
    
    /**
     * @param boolean $isApproved
     * @return \Wonderland\Application\Model\Motion
     */
    public function setIsApproved($isApproved) {
        $this->isApproved = $isApproved;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsApproved() {
        return $this->isApproved;
    }
    
    /**
     * @param int $score
     * @return \Wonderland\Application\Model\Motion
     */
    public function setScore($score) {
        $this->score = $score;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getScore() {
        return $this->score;
    }
}
