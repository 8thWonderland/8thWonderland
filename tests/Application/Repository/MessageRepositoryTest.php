<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Application\Repository\MessageRepository;

use Wonderland\Application\Model\Message;
use Wonderland\Application\Model\Member;

use Wonderland\Test\WonderlandTestCase;

class MessageRepositoryTest extends WonderlandTestCase {
    /** @var \Wonderland\Application\Repository\MessageRepository **/
    protected $repository;
    
    public function setUp() {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        $this->loadFixture('group_types');
        $this->loadFixture('groups');
        $this->loadFixture('users');
        $this->loadFixture('messages');
        
        $this->repository = new MessageRepository($this->getConnection());
    }
    
    public function testCreate() {
        $this->repository->create($this->getMessageMock());
        
        $this->assertEquals([
            'id' => '4',
            'title' => 'You have a new message !',
            'content' => 'Sorry, it was a test',
            'author_id' => '1',
            'recipient_id' => '2',
            'created_at' => '2015-08-22T15:20:00+02:00',
            'opened_at' => null,
            'deleted_by_author' => '0',
            'deleted_by_recipient' => '0'
        ], $this->repository->find(4));
    }
    
    public function testFind() {
        $this->assertEquals([
            'id' => '1',
            'title' => 'Test',
            'content' => 'test',
            'author_id' => '1',
            'recipient_id' => '2',
            'created_at' => '2015-10-03 08:39:36',
            'opened_at' => null,
            'deleted_by_author' => '0',
            'deleted_by_recipient' => '1'
        ], $this->repository->find(1));
    }
    
    public function testFindByRecipient() {
        $statement = $this->repository->findByRecipient((new Member())->setId(2));
        
        $this->assertInstanceOf('PdoStatement', $statement);
        $this->assertEquals([
            [
                'id' => '2',
                'title' => 'Test again',
                'content' => 'again',
                'author_id' => '1',
                'recipient_id' => '2',
                'created_at' => '2015-10-03 09:03:21',
                'opened_at' => null,
                'deleted_by_author' => '1',
                'deleted_by_recipient' => '0'
            ]
        ], $statement->fetchAll(\PDO::FETCH_ASSOC));
    }
    
    public function findByAuthor() {
        $statement = $this->repository->findByAuthor((new Member())->setId(1));
        
        $this->assertInstanceOf('PdoStatement', $statement);
        $this->assertEquals([
            [
                'id' => '1',
                'title' => 'Test',
                'content' => 'test',
                'author_id' => '1',
                'recipient_id' => '2',
                'created_at' => '2015-10-03 08:39:36',
                'opened_at' => null,
                'deleted_by_author' => '0',
                'deleted_by_recipient' => '1'
            ]
        ], $statement->fetchAll(\PDO::FETCH_ASSOC));
    }
    
    public function testDelete() {
        $this->assertEquals(1, $this->repository->delete(1, '1')->rowCount());
    }
    
    public function testCountUnreadMessages() {
        $this->assertEquals(2, $this->repository->countUnreadMessages(2));
    }
    
    public function getMessageMock() {
        $createdAt = new \DateTime('2015-08-22 15:20:00');
        return
            (new Message())
            ->setTitle('You have a new message !')
            ->setContent('Sorry, it was a test')
            ->setAuthor(
                (new Member())
                ->setId(1)
            )
            ->setRecipient(
                (new Member())
                ->setId(2)
            )
            ->setCreatedAt($createdAt)
        ;
    }
}