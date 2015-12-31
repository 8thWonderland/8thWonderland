<?php

namespace Wonderland\Test\Application\Manager;

use Wonderland\Application\Manager\MotionManager;

use Wonderland\Test\WonderlandTestCase;

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