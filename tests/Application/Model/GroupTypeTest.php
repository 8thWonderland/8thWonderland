<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\GroupType;

class GroupTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testModel()
    {
        $groupType =
            (new GroupType())
            ->setId(1)
            ->setLabel('groups.regional')
        ;
        $this->assertEquals(1, $groupType->getId());
        $this->assertEquals('groups.regional', $groupType->getLabel());
    }
}
