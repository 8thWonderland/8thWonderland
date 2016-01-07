<?php

namespace Wonderland\Test\Application\Manager;

use Wonderland\Application\Manager\MessageManager;

use Wonderland\Application\Model\Member;

class MessageManagerTest extends \PHPUnit_Framework_TestCase {
    /** @var \Wonderland\Application\Manager\MessageManager **/
    protected $manager;
    
    public function setUp() {
        $this->manager = new MessageManager(
            $this->getMemberManagerMock(),
            $this->getRepositoryMock()
        );
    }
    
    public function testGetReceivedMessages() {
        $messages = $this->manager->getReceivedMessages((new Member)->setId(1), 0, 15);
        
        //var_dump($messages);die;
    }
    
    public function testCountReceivedMessages() {
        $this->assertEquals(3, $this->manager->countReceivedMessages(3));
    }
    
    public function testCountUnreadMessages() {
        $this->assertEquals(1, $this->manager->countUnreadMessages(1));
    }
    
    public function getMemberManagerMock() {
        $managerMock = $this
            ->getMockBuilder('Wonderland\Application\Manager\MemberManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $managerMock
            ->expects($this->any())
            ->method('getMember')
            ->willReturnCallback([$this, 'getMemberMock'])
        ;
        return $managerMock;
    }
    
    public function getRepositoryMock() {
        $repositoryMock = $this
            ->getMockBuilder('Wonderland\Application\Repository\MessageRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('findByRecipient')
            ->willReturnCallback([$this, 'getReceivedMessagesMock'])
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('countUnreadMessages')
            ->willReturn(1)
        ;
        $repositoryMock
            ->expects($this->any())
            ->method('countReceivedMessages')
            ->willReturn(3)
        ;
        return $repositoryMock;
    }
    
    public function getMemberMock() {
        return
            (new Member())
            ->setId(5)
            ->setIdentity('Peter')
            ->setAvatar('peter_pan.jpeg')
        ;
    }
    
    public function getReceivedMessagesMock() {
        $data = [
            [
                'id' => '1',
                'title' => 'Pizza pizzaria',
                'content' => 'Here is your pizza',
                'recipient_id' => '1',
                'author_id' => '5',
                'created_at' => '2014-08-06 14:52:00',
                'opened_at' => null,
            ],
            [
                'id' => '2',
                'title' => 'Pizza pizzaria',
                'content' => 'Take your pizza',
                'recipient_id' => '1',
                'author_id' => '5',
                'created_at' => '2014-08-06 14:52:00',
                'opened_at' => '2014-08-06 14:52:00',
            ],
            [
                'id' => '2',
                'title' => 'Pizza pizzaria',
                'content' => 'Pay your pizza',
                'recipient_id' => '1',
                'author_id' => '5',
                'created_at' => '2014-08-06 14:52:00',
                'opened_at' => '2014-08-06 14:52:00',
            ],
        ];
    }
}