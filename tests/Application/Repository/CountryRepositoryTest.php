<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Test\WonderlandTestCase;
use Wonderland\Application\Repository\CountryRepository;

class CountryRepositoryTest extends WonderlandTestCase
{
    /** @var Wonderland\Application\Repository\CountryRepository **/
    protected $repository;

    public function setUp()
    {
        $this->loadFixture('countries');

        $this->repository = new CountryRepository($this->getConnection());
    }

    public function testGetCountries()
    {
        $countries = $this->repository->getCountries();

        $this->assertCount(3, $countries);
        $this->assertEquals([
            'id' => '1',
            'code' => 'fr',
            'label' => 'countries.france',
        ], $countries[0]);
    }

    public function testFind()
    {
        $country = $this->repository->find(2);

        $this->assertInstanceOf('Wonderland\\Application\\Model\\Country', $country);
        $this->assertEquals(2, $country->getId());
        $this->assertEquals('us', $country->getCode());
        $this->assertEquals('countries.united_states', $country->getLabel());
    }

    public function testFindWithUnexistingCountry()
    {
        $this->assertNull($this->repository->find(10));
    }
}
