<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Application\Repository\MotionRepository;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\MotionTheme;
use Wonderland\Application\Model\Motion;

use Wonderland\Test\WonderlandTestCase;

class MotionRepositoryTest extends WonderlandTestCase {
    /** @var \Wonderland\Application\Repository\MotionRepository **/
    protected $repository;
    
    public function setUp() {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        $this->loadFixture('group_types');
        $this->loadFixture('groups');
        $this->loadFixture('users');
        $this->loadFixture('motion_themes');
        $this->loadFixture('motions');
        $this->loadFixture('motions_votes');
        $this->loadFixture('motions_vote_tokens');
        
        $this->repository = new MotionRepository($this->getConnection());
    }
    
    public function testGetActiveMotions() {
        $this->markTestIncomplete('NOW() SQL function to replace to work with SQLite');
        
        $motions = $this->repository->getActiveMotions($this->getMemberMock());
        
        var_dump($motions);
    }
    
    public function testGetMotionThemes() {
        $motionThemes = $this->repository->getMotionThemes();
        
        $this->assertCount(3, $motionThemes);
        $this->assertEquals([
            'id' => '1',
            'label' => 'motion_themes.constitutional',
            'duration' => '8'
        ], $motionThemes[0]);
    }
    
    public function testGetMotionTheme() {
        $motionTheme = $this->repository->getMotionTheme(2);
        
        $this->assertInstanceOf('Wonderland\\Application\\Model\\MotionTheme', $motionTheme);
        $this->assertEquals('2', $motionTheme->getId());
        $this->assertEquals('motion_themes.action', $motionTheme->getLabel());
        $this->assertEquals('3', $motionTheme->getDuration());
    }
    
    public function testCreateMotion() {
        $motion = $this->getMotionMock();
        
        $this->repository->createMotion($motion);
        
        $this->assertEquals(2, $motion->getId());
    }
    
    public function testGetMotion() {
        $motion = $this->repository->getMotion(1);
        
        $this->assertInstanceOf('Wonderland\Application\Model\Motion', $motion);
        $this->assertEquals(1, $motion->getId());
        $this->assertEquals('Test des motions', $motion->getTitle());
        $this->assertEquals('test', $motion->getDescription());
        $this->assertEquals('test', $motion->getMeans());
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $motion->getAuthor());
        $this->assertFalse($motion->getIsActive());
        $this->assertFalse($motion->getIsApproved());
        $this->assertInstanceOf('DateTime', $motion->getCreatedAt());
        $this->assertInstanceOf('DateTime', $motion->getEndedAt());
    }
    
    public function testGetUnexistingMotion() {
        $this->assertNull($this->repository->getMotion(10));
    }
    
    public function testHasAlreadyVoted() {
        $this->assertTrue($this->repository->hasAlreadyVoted(1, 1));
    }
    
    public function testHasNotAlreadyVoted() {
        $this->assertFalse($this->repository->hasAlreadyVoted(1, 2));
    }
    
    public function testCreateVote() {
        $this->repository->createVote(1, 2, 'Alexander', '2016-06-08 14:50:35', '127.0.0.1', 1);
        
        $this->assertTrue($this->repository->hasAlreadyVoted(1, 2));
    }
    
    public function getMotionMock() {
        return
            (new Motion())
            ->setTitle('Eggs for dinner')
            ->setDescription('Give eggs everyday to everyone')
            ->setMeans('Chickens')
            ->setIsActive(1)
            ->setIsApproved(0)
            ->setTheme((new MotionTheme())->setId(2))
            ->setCreatedAt(new \DateTime())
            ->setEndedAt(new \DateTime())
            ->setAuthor($this->getMemberMock())
        ;
    }
    
    public function getMemberMock() {
        return
            (new Member())
            ->setId(2)
            ->setIdentity('Alexander')
        ;
    }
}