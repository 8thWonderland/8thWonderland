<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Motion;
use Wonderland\Application\Model\MotionTheme;
use Wonderland\Application\Model\Member;

class MotionTest extends \PHPUnit_Framework_TestCase {
    public function testModel() {
        $motion =
            (new Motion())
            ->setId(1)
            ->setTitle('Motion A')
            ->setDescription('Description de la motion A')
            ->setMeans('Accepter la motion')
            ->setIsActive(1)
            ->setIsApproved(0)
            ->setScore(76)
            ->setTheme(new MotionTheme())
            ->setAuthor(new Member())
            ->setCreatedAt(new \DateTime())
            ->setEndedAt(new \DateTime())
        ;
        $this->assertEquals(1, $motion->getId());
        $this->assertEquals('Motion A', $motion->getTitle());
        $this->assertEquals('Description de la motion A', $motion->getDescription());
        $this->assertEquals('Accepter la motion', $motion->getMeans());
        $this->assertEquals(1, $motion->getIsActive());
        $this->assertEquals(0, $motion->getIsApproved());
        $this->assertEquals(76, $motion->getScore());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\MotionTheme', $motion->getTheme());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $motion->getAuthor());
        $this->assertInstanceOf('DateTime', $motion->getCreatedAt());
        $this->assertInstanceOf('DateTime', $motion->getEndedAt());
    }
}