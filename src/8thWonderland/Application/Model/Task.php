<?php

namespace Wonderland\Application\Model;

class Task
{
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $description;
    /** @var \DateTime **/
    protected $createdAt;
    /** @var int **/
    protected $group;
    /** @var int **/
    protected $status;
    /** @var int **/
    protected $author;

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Task
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
     * @param string $description
     *
     * @return \Wonderland\Application\Model\Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Wonderland\Application\Model\Task
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
     * @param int $group
     *
     * @return \Wonderland\Application\Model\Task
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param int $status
     *
     * @return \Wonderland\Application\Model\Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $author
     *
     * @return \Wonderland\Application\Model\Task
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
