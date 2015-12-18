<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Member;
use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\Country;
use Wonderland\Application\Model\Region;

class MemberTest extends \PHPUnit_Framework_TestCase {
    public function testModel() {
        $member =
            (new Member())
            ->setId(1)
            ->setLogin('john_doe')
            ->setPassword('toronto987123')
            ->setSalt('f4d5f1f3q3f344njh')
            ->setIdentity('John Doe')
            ->setEmail('john_doe@gmail.com')
            ->setAvatar('avatar.png')
            ->setCountry(new Country())
            ->setRegion(new Region())
            ->setLanguage('FR')
            ->setIsEnabled(1)
            ->setIsBanned(0)
            ->setCreatedAt(new \DateTime())
            ->setLastConnectedAt(new \DateTime())
            ->addGroup((new Group())->setId(1))
            ->addGroup((new Group())->setId(2))
            ->removeGroup(2)
        ;
        $this->assertEquals(1, $member->getId());
        $this->assertEquals('john_doe', $member->getLogin());
        $this->assertEquals('toronto987123', $member->getPassword());
        $this->assertEquals('f4d5f1f3q3f344njh', $member->getSalt());
        $this->assertEquals('John Doe', $member->getIdentity());
        $this->assertEquals('john_doe@gmail.com', $member->getEmail());
        $this->assertEquals('avatar.png', $member->getAvatar());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Country', $member->getCountry());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Region', $member->getRegion());
        $this->assertEquals('FR', $member->getLanguage());
        $this->assertEquals(1, $member->getIsEnabled());
        $this->assertEquals(0, $member->getIsBanned());
        $this->assertInstanceOf('DateTime', $member->getCreatedAt());
        $this->assertInstanceOf('DateTime', $member->getLastConnectedAt());
        $this->assertCount(1, $member->getGroups());
    }
}