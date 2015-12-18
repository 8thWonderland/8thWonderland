<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Application\Repository\MotionRepository;

use Wonderland\Application\Model\Member;

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
        $this->loadFixture('motions');
        $this->loadFixture('motions_vote_tokens');
        
        $this->repository = new MotionRepository($this->getConnection());
    }
    
    public function testGetActiveMotions() {
        $this->markTestIncomplete('NOW() SQL function to replace to work with SQLite');
        
        $motions = $this->repository->getActiveMotions($this->getMemberMock());
        
        var_dump($motions);
    }
    
    public function getMemberMock() {
        return
            (new Member())
            ->setId(2)
            ->setIdentity('Alexander')
        ;
    }
}