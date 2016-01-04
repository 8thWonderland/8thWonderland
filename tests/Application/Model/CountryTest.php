<?php

namespace Wonderland\Test\Application\Model;

use Wonderland\Application\Model\Country;

class CountryTest extends \PHPUnit_Framework_TestCase
{
    public function testModel()
    {
        $country =
            (new Country())
            ->setId(1)
            ->setLabel('countries.france')
            ->setCode('FR')
        ;
        $this->assertEquals(1, $country->getId());
        $this->assertEquals('countries.france', $country->getLabel());
        $this->assertEquals('FR', $country->getCode());
    }
}
