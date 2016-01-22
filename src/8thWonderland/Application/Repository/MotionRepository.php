<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;
use Wonderland\Application\Model\MotionTheme;

class MotionRepository extends AbstractRepository
{
    /**
     * @param \Wonderland\Application\Model\Member $member
     *
     * @return array|Member
     */
    public function getActiveMotions(Member $member)
    {
        $statement = $this->connection->prepareStatement(
            'SELECT m.id, m.title, m.created_at, m.ended_at, !ISNULL(mvt.citizen_id) as has_already_voted, m.is_active '.
            'FROM motions m '.
            'LEFT JOIN motions_vote_tokens mvt ON mvt.motion_id = m.id AND mvt.citizen_id = :citizen_id '.
            'WHERE m.is_active = 1 '.
            'ORDER BY m.ended_at DESC ', ['citizen_id' => $member->getId()]);

        $result = [];
        while ($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $motion = $this->formatObject($data);
            // We add the motion to the active motions list only if its vote isn't finished yet
            if(!$this->checkMotion($motion)) {
                $result[] = [
                    'motion' => $motion,
                    'has_already_voted' => (bool) $data['has_already_voted']
                ];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getMotionThemes()
    {
        return $this->connection->query(
            'SELECT id, label, duration FROM motion_themes'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int $motionThemeId
     *
     * @return array|null
     */
    public function getMotionTheme($motionThemeId)
    {
        $data = $this->connection->prepareStatement(
            'SELECT id, label, duration FROM motion_themes WHERE id = :id', ['id' => $motionThemeId])->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return;
        }

        return
            (new MotionTheme())
            ->setId($data['id'])
            ->setLabel($data['label'])
            ->setDuration($data['duration'])
        ;
    }

    /**
     * @param \Wonderland\Application\Model\Motion $motion
     */
    public function createMotion(Motion &$motion)
    {
        $affectedRows = $this->connection->prepareStatement(
            'INSERT INTO motions(theme_id, title, description, means, author_id, '.
            'created_at, ended_at, is_active, is_approved) VALUES(:theme_id, :title, '.
            ':description, :means, :author_id, :created_at, :ended_at, :is_active, :is_approved)', [
            'theme_id' => $motion->getTheme()->getId(),
            'title' => $motion->getTitle(),
            'description' => $motion->getDescription(),
            'means' => $motion->getMeans(),
            'author_id' => $motion->getAuthor()->getId(),
            'created_at' => $motion->getCreatedAt()->format('c'),
            'ended_at' => $motion->getEndedAt()->format('c'),
            'is_active' => $motion->getIsActive(),
            'is_approved' => $motion->getIsApproved(),
        ])->rowCount();
        if ($affectedRows === 0) {
            $this->throwPdoException();
        }
        $motion->setId($this->connection->lastInsertId());
    }
    
    /**
     * @param int $minRange
     * @param int $maxRange
     * @return array
     */
    public function getMotions($minRange, $maxRange) {
        $statement = $this->connection->query(
            'SELECT m.id, m.title, mt.id as theme_id, mt.label as theme_label, '.
            'm.description, m.means, m.created_at, m.ended_at, m.is_active, m.is_approved, '.
            'm.score, u.id as author_id, u.identity as author_identity '.
            'FROM motions m '.
            'INNER JOIN motion_themes mt ON mt.id = m.theme_id '.
            'INNER JOIN users u ON m.author_id = u.id '.
            'ORDER BY m.ended_at DESC ' .
            $this->getRangeStatements($minRange, $maxRange)
        );
        $motions = [];
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $motions[] = $this->formatObject($data);
        }
        return $motions;
    }

    /**
     * @param int $motionId
     *
     * @return \Wonderland\Application\Model\Motion|null
     */
    public function getMotion($motionId)
    {
        $data = $this->connection->prepareStatement(
            'SELECT m.id, m.title, mt.id as theme_id, mt.label as theme_label, '.
            'm.description, m.means, m.created_at, m.ended_at, m.is_active, m.is_approved, '.
            'm.score, u.id as author_id, u.identity as author_identity '.
            'FROM motions m '.
            'INNER JOIN motion_themes mt ON mt.id = m.theme_id '.
            'INNER JOIN users u ON m.author_id = u.id '.
            'WHERE m.id = :motion_id', ['motion_id' => $motionId])->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return;
        }

        return $this->formatObject($data);
    }

    /**
     * @param int $motionId
     * @param int $memberId
     *
     * @return bool
     */
    public function hasAlreadyVoted($motionId, $memberId)
    {
        return (bool) $this->connection->prepareStatement(
            'SELECT COUNT(*) AS count FROM motions_vote_tokens WHERE motion_id = :motion_id AND citizen_id = :citizen_id', ['motion_id' => $motionId, 'citizen_id' => $memberId])->fetch(\PDO::FETCH_ASSOC)['count'];
    }

    /**
     * @param int    $motionId
     * @param int    $memberId
     * @param string $memberIdentity
     * @param string $date
     * @param string $ip
     * @param bool   $vote
     *
     * @return type
     */
    public function createVote($motionId, $memberId, $memberIdentity, $date, $ip, $vote)
    {
        $this->beginTransaction();
        $voteTokenStatement = $this->connection->prepareStatement(
            'INSERT INTO motions_vote_tokens (motion_id, citizen_id, date, ip, browser) '.
            'VALUES (:motion_id, :citizen_id, :date, :ip, "")', [
            'motion_id' => $motionId,
            'citizen_id' => $memberId,
            'date' => $date,
            'ip' => $ip,
        ]);
        if ($voteTokenStatement->rowCount() === 0) {
            $this->rollback(true);
        }
        unset($voteTokenStatement);
        $hash = hash('sha512', "{$this->connection->lastInsertId()}#$motionId#$memberIdentity#$vote#$date#$ip");
        $voteStatement = $this->connection->prepareStatement(
            'INSERT INTO motions_votes(motion_id, choice, hash)  VALUES (:id, :choice, :hash)', [
            'id' => $motionId,
            'choice' => $vote,
            'hash' => $hash,
        ]);
        if ($voteStatement->rowCount() === 0) {
            $this->rollback(true);
        }
        $this->commit();
    }
    
    public function checkMotion(Motion $motion) {
        if(!$motion->getIsActive() || $motion->getEndedAt() > new \DateTime()) {
            return false;
        }
        $this->beginTransaction();
        
        $votes = $this->getVotes($motion);
        
        $isApproved = $votes['positive'] > $votes['negative'];
        $score = round(100 - ($votes[($isApproved) ? 'negative' : 'positive'] / ($votes['negative'] + $votes['positive'])) * 100, 2);
        
        $motion
            ->setIsActive(false)
            ->setIsApproved($isApproved)
            ->setScore($score)
        ;
        
        $nbAffectedRows = $this->connection->prepareStatement(
            'UPDATE motions SET is_active = 0, is_approved = :is_approved, score = :score WHERE id = :id'
        , [
            'is_approved' => $isApproved,
            'score' => $score,
            'id' => $motion->getId()
        ])->rowCount();
        if($nbAffectedRows === 0) {
            $this->rollback(true);
        }
        $this->commit();
        return true;
    }
    
    public function getVotes(Motion $motion) {
        $statement = $this->connection->prepareStatement(
            'SELECT COUNT(*) as nb_votes, choice FROM motions_votes WHERE motion_id = :motion_id GROUP BY choice'
        , ['motion_id' => $motion->getId()]);
        
        $votes = [
            'positive' => 0,
            'negative' => 0
        ];
        
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $votes[((bool)$data['choice']) ? 'positive' : 'negative'] = (int) $data['nb_votes'];
        }
        return $votes;
    }
    
    /**
     * @return int
     */
    public function countMotions() {
        return (int) $this->connection->query(
            'SELECT COUNT(*) as nb_motions FROM motions'
        )->fetch(\PDO::FETCH_ASSOC)['nb_motions'];
    }
    
    /**
     * @return int
     */
    public function countArchivedMotions() {
        return (int) $this->connection->query(
            'SELECT COUNT(*) as nb_motions FROM motions WHERE is_active = 0'
        )->fetch(\PDO::FETCH_ASSOC)['nb_motions'];
    }
    
    /**
     * @param int $minRange
     * @param int $maxRange
     * @return array
     */
    public function getArchives($minRange, $maxRange) {
        $statement = $this->connection->query(
            'SELECT m.id, m.title, mt.id as theme_id, mt.label as theme_label, '.
            'm.description, m.means, m.created_at, m.ended_at, m.is_active, m.is_approved, '.
            'm.score, u.id as author_id, u.identity as author_identity '.
            'FROM motions m '.
            'INNER JOIN motion_themes mt ON mt.id = m.theme_id '.
            'INNER JOIN users u ON m.author_id = u.id '.
            'WHERE m.is_active = 0 ' .
            'ORDER BY m.ended_at DESC ' .
            $this->getRangeStatements($minRange, $maxRange)
        );
        $motions = [];
        while($data = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $motions[] = $this->formatObject($data);
        }
        return $motions;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function formatArray($data)
    {
        $result = [
            'id' => $data['id'],
            'title' => $data['title'],
            'created_at' => $data['created_at'],
            'ended_at' => $data['ended_at'],
        ];

        if (isset($data['has_already_voted'])) {
            $result['has_already_voted'] = $data['has_already_voted'];
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return Member
     */
    public function formatObject($data)
    {
        return
            (new Motion())
            ->setId((int) $data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setMeans($data['means'])
            ->setIsActive((bool) $data['is_active'])
            ->setIsApproved((bool) $data['is_approved'])
            ->setScore((float) $data['score'])
            ->setTheme(
                (new MotionTheme())
                ->setId((int) $data['theme_id'])
                ->setLabel($data['theme_label'])
            )
            ->setAuthor(
                (new Member())
                ->setId((int) $data['author_id'])
                ->setIdentity($data['author_identity'])
            )
            ->setCreatedAt(new \DateTime($data['created_at']))
            ->setEndedAt(new \DateTime($data['ended_at']))
        ;
    }
}
