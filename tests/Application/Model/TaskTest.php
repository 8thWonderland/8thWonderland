<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Task;

class TaskTest extends \PHPUnit_Framework_TestCase {
    public function testModel() {
        $task =
            (new Task())
            ->setId(1)
            ->setAuthor(1)
            ->setGroup(1)
            ->setStatus('ON')
            ->setDescription('cron OVH')
            ->setCreatedAt(new \DateTime())
        ;
        $this->assertEquals(1, $task->getId());
        $this->assertEquals(1, $task->getAuthor());
        $this->assertEquals(1, $task->getGroup());
        $this->assertEquals('ON', $task->getStatus());
        $this->assertEquals('cron OVH', $task->getDescription());
        $this->assertInstanceOf('DateTime', $task->getCreatedAt());
    }
}