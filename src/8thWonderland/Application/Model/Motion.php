<?php

namespace Wonderland\Application\Model;

class Motion implements \JsonSerializable
{
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
    /** @var bool **/
    protected $isActive;
    /** @var bool **/
    protected $isApproved;
    /** @var int **/
    protected $score;

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Motion
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
     * @param \Wonderland\Application\Model\MotionTheme $theme
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setTheme(MotionTheme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\MotionTheme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $title
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return strin
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @return \Wonderland\Application\Model\Motion
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
     * @param string $means
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setMeans($means)
    {
        $this->means = $means;

        return $this;
    }

    /**
     * @return string
     */
    public function getMeans()
    {
        return $this->means;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Wonderland\Application\Model\Motion
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
     * @param \DateTime $endedAt
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setEndedAt(\DateTime $endedAt)
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param \Wonderland\Application\Model\Member $author
     *
     * @return \Wonderland\Application\Model\Motion
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
     * @param bool $isActive
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isApproved
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * @param float $score
     *
     * @return \Wonderland\Application\Model\Motion
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'means' => $this->means,
            'theme' => $this->theme,
            'author' => $this->author,
            'created_at' => $this->createdAt,
            'ended_at' => $this->endedAt,
            'is_active' => $this->isActive,
            'is_approved' => $this->isApproved,
            'score' => $this->score,
        ];
    }
}
