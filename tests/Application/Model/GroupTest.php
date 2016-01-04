<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Group;
use Wonderland\Application\Model\GroupType;
use Wonderland\Application\Model\Member;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testModel()
    {
        $group =
            (new Group())
            ->setId(1)
            ->setName('Île-de-France')
            ->setDescription('Groupe Régional d\'Île-de-France')
            ->setType(new GroupType())
            ->setContact(new Member())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;
        $this->assertEquals(1, $group->getId());
        $this->assertEquals('Île-de-France', $group->getName());
        $this->assertEquals('Groupe Régional d\'Île-de-France', $group->getDescription());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\GroupType', $group->getType());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Member', $group->getContact());
        $this->assertInstanceOf('DateTime', $group->getCreatedAt());
        $this->assertInstanceOf('DateTime', $group->getUpdatedAt());
    }
}
