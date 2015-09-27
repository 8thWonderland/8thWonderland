<?php

namespace Wonderland\Application\Model;

class Message {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $title;
    /** @var string **/
    protected $content;
    /** @var string **/
    protected $author;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var array **/
    protected $recipients;
    
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
     * @param string $author
     * @return \Wonderland\Application\Model\Message
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
     * @param array $recipients
     * @return \Wonderland\Application\Model\Message
     */
    public function setRecipients($recipients) {
        $this->recipients = $recipients;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getRecipients() {
        return $this->recipients;
    }
}