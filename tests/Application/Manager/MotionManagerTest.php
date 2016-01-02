<?php

namespace Wonderland\Test\Application\Manager;

use Wonderland\Application\Manager\MotionManager;

use Wonderland\Test\WonderlandTestCase;

use Wonderland\Library\Exception\NotFoundException;

use Wonderland\Application\Model\MotionTheme;
use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Motion;

class MotionManagerTest extends WonderlandTestCase {
    protected $manager;
    
    public function setUp() {
        $this->manager = new MotionManager(
            $this->getConnection(),
            $this->getTranslatorMock(),
            $this->getRepositoryMock()
        );
    }
    
    public function testGetMotionThemes() {
        $this->assertEquals(
            $this->getMotionThemesMock(),
            $this->manager->getMotionThemes()
        );
    }
    
    public function testCreateMotion() {
        $motion = $this->manager->createMotion(
            'Eggs for dinner',
            'eat eggs everyday',
            2,
            $this->getAuthorMock(),
            'chickens'
        );
        
        $this->assertInstanceOf('Wonderland\Application\Model\Motion', $motion);
        $this->assertEquals('2', $motion->getId());
    }
    
    public function testGetMotion() {
        $motion = $this->manager->getMotion(1);
        
        $this->assertInstanceOf('Wonderland\Application\Model\Motion', $motion);
        $this->assertEquals(1, $motion->getId());
        $this->assertEquals('Eggs for dinner', $motion->getTitle());
        $this->assertEquals('Give eggs everyday to everyone', $motion->getDescription());
        $this->assertEquals('Chickens', $motion->getMeans());
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $motion->getAuthor());
        $this->assertTrue($motion->getIsActive());
        $this->assertFalse($motion->getIsApproved());
        $this->assertInstanceOf('DateTime', $motion->getCreatedAt());
        $this->assertInstanceOf('DateTime', $motion->getEndedAt());
    }
    
    /**
     * @expectedException \Wonderland\Library\Exception\NotFoundException
     * @expectedMessage Motion Not Found
     */
    public function testGetUnexistingMotion() {
        $this->manager->getMotion(15);
    }
    
    public function getTranslatorMock() {
        $translatorMock = $this
            ->getMockBuilder('Wonderland\Library\Translator')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return $translatorMock;
    }
    
    public function getRepositoryMock() {
        $repositoryMock = $this
            ->getMockBuilder('Wonderland\Application\Repository\MotionRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('getMotion')
            ->willReturnCallback([$this, 'getMotionMock'])
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('getMotionThemes')
            ->willReturnCallback([$this, 'getMotionThemesMock'])
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('getMotionTheme')
            ->willReturnCallback([$this, 'getMotionThemeMock'])
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('createMotion')
            ->willReturnCallback([$this, 'createMotionMock'])
        ;
        return $repositoryMock;
    }
    
    public function getMotionMock($motionId) {
        if($motionId > 10) {
            throw new NotFoundException('Motion Not Found');
        }
        return
            (new Motion())
            ->setId(1)
            ->setTitle('Eggs for dinner')
            ->setDescription('Give eggs everyday to everyone')
            ->setMeans('Chickens')
            ->setIsActive(true)
            ->setIsApproved(false)
            ->setTheme((new MotionTheme())->setId(2))
            ->setCreatedAt(new \DateTime())
            ->setEndedAt(new \DateTime())
            ->setAuthor($this->getAuthorMock())
        ;
    }
    
    public function getMotionThemesMock() {
        return [
            [
                'id' => '1',
                'label' => 'motion_themes.constitutional',
                'duration' => '8'
            ],
            [
                'id' => '2',
                'label' => 'motion_themes.action',
                'duration' => '3'
            ],
            [
                'id' => '3',
                'label' => 'motion_themes.emergency',
                'duration' => '1'
            ]
        ];
    }
    
    public function getMotionThemeMock() {
        return
            (new MotionTheme())
            ->setId('2')
            ->setLabel('motion_themes.action')
            ->setDuration('3')
        ;
    }
    
    public function createMotionMock(Motion &$motion) {
        $motion->setId(2);
    }
    
    public function getAuthorMock() {
        return
            (new Member())
            ->setId(3)
        ;
    }
}