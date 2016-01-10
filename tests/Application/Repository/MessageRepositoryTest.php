<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Application\Repository\MessageRepository;
use Wonderland\Application\Model\Message;
use Wonderland\Application\Model\Member;
use Wonderland\Test\WonderlandTestCase;

class MessageRepositoryTest extends WonderlandTestCase
{
    /** @var \Wonderland\Application\Repository\MessageRepository **/
    protected $repository;

    public function setUp()
    {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        $this->loadFixture('group_types');
        $this->loadFixture('groups');
        $this->loadFixture('users');
        $this->loadFixture('messages');

        $this->repository = new MessageRepository($this->getConnection());
    }

    public function testCreate()
    {
        $this->repository->create($this->getMessageMock());

        $message = $this->repository->find(1, 4);
        
        $this->assertInstanceOf('Wonderland\Application\Model\Message', $message);
        $this->assertEquals(4, $message->getId());
        $this->assertEquals('You have a new message !', $message->getTitle());
        $this->assertEquals('Sorry, it was a test', $message->getContent());
        $this->assertInstanceOf('DateTime', $message->getCreatedAt());
        $this->assertNull($message->getOpenedAt());
        
        $author = $message->getAuthor();
        
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $author);
        $this->assertEquals(1, $author->getId());
        $this->assertEquals('John Doe', $author->getIdentity());
        $this->assertEquals('avatar.png', $author->getAvatar());
        
        $recipient = $message->getRecipient();
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $recipient);
        $this->assertEquals(2, $recipient->getId());
        $this->assertEquals('Alexander', $recipient->getIdentity());
        $this->assertEquals('avatar.jpg', $recipient->getAvatar());
    }

    public function testFind()
    {
        $message = $this->repository->find(1, 1);
        
        $this->assertInstanceOf('Wonderland\Application\Model\Message', $message);
        $this->assertEquals(1, $message->getId());
        $this->assertEquals('Test', $message->getTitle());
        $this->assertEquals('test', $message->getContent());
        $this->assertInstanceOf('DateTime', $message->getCreatedAt());
        $this->assertNull($message->getOpenedAt());
        
        $author = $message->getAuthor();
        
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $author);
        $this->assertEquals(1, $author->getId());
        $this->assertEquals('John Doe', $author->getIdentity());
        $this->assertEquals('avatar.png', $author->getAvatar());
        
        $recipient = $message->getRecipient();
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $recipient);
        $this->assertEquals(2, $recipient->getId());
        $this->assertEquals('Alexander', $recipient->getIdentity());
        $this->assertEquals('avatar.jpg', $recipient->getAvatar());
    }

    public function testFindByRecipient()
    {
        $messages = $this->repository->findByRecipient(
            (new Member())->setId(2)->setIdentity('Alexander')->setAvatar('avatar.jpg'),
            0,
            15
        );
        
        $this->assertCount(1, $messages);
        
        $message = $messages[0];
        
        $this->assertInstanceOf('Wonderland\Application\Model\Message', $message);
        $this->assertEquals(2, $message->getId());
        $this->assertEquals('Test again', $message->getTitle());
        $this->assertEquals('again', $message->getContent());
        $this->assertInstanceOf('DateTime', $message->getCreatedAt());
        $this->assertNull($message->getOpenedAt());
        
        $author = $message->getAuthor();
        
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $author);
        $this->assertEquals(1, $author->getId());
        $this->assertEquals('John Doe', $author->getIdentity());
        $this->assertEquals('avatar.png', $author->getAvatar());
        
        $recipient = $message->getRecipient();
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $recipient);
        $this->assertEquals(2, $recipient->getId());
        $this->assertEquals('Alexander', $recipient->getIdentity());
        $this->assertEquals('avatar.jpg', $recipient->getAvatar());
    }

    public function findByAuthor()
    {
        $messages = $this->repository->findByAuthor(
            (new Member())->setId(1)->setIdentity('John Doe')->setAvatar('avatar.png')
        );

        $this->assertCount(1, $messages);
        
        $message = $messages[0];
        
        $this->assertInstanceOf('Wonderland\Application\Model\Message', $message);
        $this->assertEquals(1, $message->getId());
        $this->assertEquals('Test', $message->getTitle());
        $this->assertEquals('test', $message->getContent());
        $this->assertNull($message->getOpenedAt());
        $this->assertInstanceOf('DateTime', $message->getCreateAt());
        
        $author = $message->getAuthor();
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $author);
        $this->assertEquals(2, $author->getId());
        $this->assertEquals('Alexander', $author->getIdentity());
        $this->assertEquals('avatar.jpg', $author->getAvatar());
        
        $recipient = $message->getRecipient();
        $this->assertInstanceOf('Wonderland\Application\Model\Member', $recipient);
        $this->assertEquals(2, $recipient->getId());
        $this->assertEquals('Alexander', $recipient->getIdentity());
        $this->assertEquals('avatar.jpg', $recipient->getAvatar());
    }

    public function testDelete()
    {
        $this->assertEquals(1, $this->repository->delete(1, '1')->rowCount());
    }

    public function testCountUnreadMessages()
    {
        $this->assertEquals(1, $this->repository->countUnreadMessages(2));
    }

    public function getMessageMock()
    {
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
