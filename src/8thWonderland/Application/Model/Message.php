<?php

namespace Wonderland\Application\Model;

class Message {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $title;
    /** @var string **/
    protected $content;
    /** @var \Wonderland\Application\Model\Member **/
    protected $author;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var \Wonderland\Application\Model\Member **/
    protected $recipient;
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Message
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
     * @param string $title
     * @return \Wonderland\Application\Model\Message
     */
    public function setTitle($title) {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * @param string $content
     * @return \Wonderland\Application\Model\Message
     */
    public function setContent($content) {
        $this->content = $content;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * @param \Wonderland\Application\Model\Member $author
     * @return \Wonderland\Application\Model\Message
     */
    public function setAuthor(Member $author) {
        $this->author = $author;
        
        return $this;
    }
    
    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getAuthor() {
        return $this->author;
    }
    
    /**
     * @param \DateTime $createdAt
     * @return \Wonderland\Application\Model\Message
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
     * @param \Wonderland\Application\Model\Member $recipient
     * @return \Wonderland\Application\Model\Message
     */
    public function setRecipient(Member $recipient) {
        $this->recipient = $recipient;
        
        return $this;
    }
    
    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getRecipient() {
        return $this->recipient;
    }
}