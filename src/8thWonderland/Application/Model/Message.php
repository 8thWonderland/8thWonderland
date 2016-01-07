<?php

namespace Wonderland\Application\Model;

class Message implements \JsonSerializable
{
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
    /** @var \DateTime **/
    protected $openedAt;
    /** @var \Wonderland\Application\Model\Member **/
    protected $recipient;

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Message
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
     * @param string $title
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $content
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function setAuthor(Member $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Wonderland\Application\Model\Message
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
     * @param \DateTime|null $openedAt
     * @return \Wonderland\Application\Model\Message
     */
    public function setOpenedAt($openedAt) {
        $this->openedAt = $openedAt;
        
        return $this;
    }
    
    public function getOpenedAt() {
        return $this->openedAt;
    }

    /**
     * @param \Wonderland\Application\Model\Member $recipient
     *
     * @return \Wonderland\Application\Model\Message
     */
    public function setRecipient(Member $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\Member
     */
    public function getRecipient()
    {
        return $this->recipient;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author' => [
                'id' => $this->author->getId(),
                'identity' => $this->author->getIdentity(),
                'avatar' => $this->author->getAvatar()
            ],
            'recipient' => [
                'id' => $this->recipient->getId(),
                'identity' => $this->recipient->getIdentity(),
                'avatar' => $this->recipient->getAvatar()
            ],
            'created_at' => $this->createdAt,
            'opened_at' => $this->openedAt
        ];
    }
}
