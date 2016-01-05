<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;
use Wonderland\Library\Exception\NotFoundException;
use Wonderland\Library\Exception\BadRequestException;
use Wonderland\Library\Exception\RuntimeException;
use Wonderland\Application\Repository\MotionRepository;

class MotionManager
{
    /** @var \Wonderland\Application\Repository\MotionRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Application\Repository\MotionRepository $repository
     */
    public function __construct(MotionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string                               $title
     * @param string                               $description
     * @param int                                  $themeId
     * @param \Wonderland\Application\Model\Member $author
     * @param string                               $means
     *
     * @return \Wonderland\Application\Model\Motion
     *
     * @throws \Wonderland\Library\Exception\NotFoundException
     */
    public function createMotion($title, $description, $themeId, Member $author, $means)
    {
        if (($theme = $this->repository->getMotionTheme($themeId)) === null) {
            throw new NotFoundException('Motion Theme Not Found');
        }

        $endedAt = new \DateTime();
        $endedAt->add(new \DateInterval("P{$theme->getDuration()}D"));

        $motion =
            (new Motion())
            ->setTitle($title)
            ->setDescription($description)
            ->setMeans($means)
            ->setAuthor($author)
            ->setTheme($theme)
            ->setIsActive(1)
            ->setIsApproved(0)
            ->setCreatedAt(new \DateTime())
            ->setEndedAt($endedAt)
        ;
        $this->repository->createMotion($motion);

        return $motion;
    }

    /**
     * @param Member $member
     *
     * @return array
     */
    public function getActiveMotions(Member $member)
    {
        return $this->repository->getActiveMotions($member);
    }

    /**
     * @param int $motionId
     *
     * @return \Wonderland\Application\Model\Motion
     *
     * @throws NotFoundException
     */
    public function getMotion($motionId)
    {
        if (($motion = $this->repository->getMotion($motionId)) === null) {
            throw new NotFoundException('Motion Not Found');
        }
        $this->repository->checkMotion($motion);
        return $motion;
    }

    /**
     * @return array
     */
    public function getMotionThemes()
    {
        return $this->repository->getMotionThemes();
    }

    /**
     * Returns number of affected rows.
     * 
     * @param \Wonderland\Application\Model\Member $member
     * @param int                                  $motionId
     * @param string                               $vote
     */
    public function voteMotion(Member $member, $motionId, $vote)
    {
        if ($this->hasAlreadyVoted($motionId, $member->getId())) {
            throw new BadRequestException('You already voted this motion');
        }

        $date = date('Y-m-d h-i-s');
        $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';

        try {
            $this->repository->createVote($motionId, $member->getId(), $member->getIdentity(), $date, $ip, $vote);
        } catch (\PDOException $exception) {
            throw new RuntimeException("The vote failed : {$exception->getMessage()}");
        }
    }

    /**
     * @param int $motionId
     * @param int $memberId
     *
     * @return bool
     */
    public function hasAlreadyVoted($motionId, $memberId)
    {
        return $this->repository->hasAlreadyVoted($motionId, $memberId);
    }
}
