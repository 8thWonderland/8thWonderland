<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Message;
use Wonderland\Application\Model\Member;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testModel()
    {
        $message =
            (new Message())
            ->setId(1)
            ->setTitle('Hello world')
            ->setContent('Hello everyone !')
            ->setAuthor(new Member())
            ->setRecipient(new Member())
            ->setCreatedAt(new \DateTime())
        ;
        $this->assertEquals(1, $message->getId());
        $this->assertEquals('Hello world', $message->getTitle());
        $this->assertEquals('Hello everyone !', $message->getContent());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $message->getAuthor());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $message->getRecipient());
        $this->assertInstanceOf('DateTime', $message->getCreatedAt());
    }
}
