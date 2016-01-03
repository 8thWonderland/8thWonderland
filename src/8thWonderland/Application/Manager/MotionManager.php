<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;

use Wonderland\Library\Exception\NotFoundException;

use Wonderland\Library\Database\PdoDriver;

use Wonderland\Application\Repository\MotionRepository;

use Wonderland\Library\Translator;

class MotionManager {
    /** @var \Wonderland\Library\Database\PdoDriver **/
    protected $connection;
    /** @var \Wonderland\Library\Translator **/
    protected $translator;
    /** @var \Wonderland\Application\Repository\MotionRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Library\Database\PdoDriver $connection
     * @param \Wonderland\Library\Translator $translator
     * @param \Wonderland\Application\Repository\MotionRepository $repository
     */
    public function __construct(PdoDriver $connection, Translator $translator, MotionRepository $repository) {
        $this->connection = $connection;
        $this->translator = $translator;
        $this->repository = $repository;
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param int $themeId
     * @param \Wonderland\Application\Model\Member $author
     * @param string $means
     * @return \Wonderland\Application\Model\Motion
     * @throws \Wonderland\Library\Exception\NotFoundException
     */
    public function createMotion($title, $description, $themeId, Member $author, $means) {
        if(($theme = $this->repository->getMotionTheme($themeId)) === null) {
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
     * @return array
     */
    public function getActiveMotions(Member $member) {
        return $this->repository->getActiveMotions($member);
    }
    
    /**
     * @param int $motionId
     * @return \Wonderland\Application\Model\Motion
     * @throws NotFoundException
     */
    public function getMotion($motionId) {
        if(($motion = $this->repository->getMotion($motionId)) === null) {
            throw new NotFoundException('Motion Not Found');
        }
        return $motion;
    }
    
    /**
     * @return array
     */
    public function getMotionThemes() {
        return $this->repository->getMotionThemes();
    }
    
    /**
     * Returns number of affected rows
     * 
     * @param \Wonderland\Application\Model\Member $member
     * @param int $id
     * @param string $vote
     * @return int
     */
    public function voteMotion(Member $member, $id, $vote) {
        $date = date('Y-m-d h-i-s');
        $ip =
            (isset($_SERVER['REMOTE_ADDR']))
            ? $_SERVER['REMOTE_ADDR']
            : 'inconnue'
        ;
        $statement = $this->connection->prepareStatement(
            'INSERT INTO motions_vote_tokens (motion_id, citizen_id, date, ip) ' .
            'VALUES (:motion_id, :citizen_id, :date, :ip)'
        , [
            'motion_id' => $id,
            'citizen_id' => $member->getId(),
            'date' => $date,
            'ip' => $ip
        ]);
        if ($statement->rowCount() === 0) {
            return 0;
        }
        
        $hash = hash('sha512', "{$this->connection->lastInsertId()}#$id#{$member->getIdentity()}#$vote#$date#$ip");
        $statement = $this->connection->prepareStatement(
            'INSERT INTO motions_votes(motion_id, choice, hash)  VALUES (:id, :choice, :hash)'
        , [
            'id' => $id,
            'choice' => $vote,
            'hash' => $hash
        ]);
        return $statement->rowCount();
    }
    
    /**
     * @param int $motionId
     * @param int $memberId
     * @return bool
     */
    public function hasAlreadyVoted($motionId, $memberId) {
        return $this->repository->hasAlreadyVoted($motionId, $memberId);
    }
}