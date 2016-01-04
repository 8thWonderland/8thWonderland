<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Region;
use Wonderland\Application\Model\Country;

class RegionTest extends \PHPUnit_Framework_TestCase
{
    public function testModel()
    {
        $region =
            (new Region())
            ->setId(1)
            ->setCountry(new Country())
            ->setName('Île-de-France')
            ->setLatitude(12.355)
            ->setLongitude(25.336)
            ->setCreatedAt(new \DateTime())
        ;
        $this->assertEquals(1, $region->getId());
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Country', $region->getCountry());
        $this->assertEquals('Île-de-France', $region->getName());
        $this->assertEquals(12.355, $region->getLatitude());
        $this->assertEquals(25.336, $region->getLongitude());
        $this->assertInstanceOf('DateTime', $region->getCreatedAt());
    }
}
