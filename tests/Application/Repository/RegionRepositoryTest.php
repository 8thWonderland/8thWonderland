<?php

namespace Wonderland\Test\Application\Repository;

use Wonderland\Test\WonderlandTestCase;

use Wonderland\Application\Repository\RegionRepository;

class RegionRepositoryTest extends WonderlandTestCase {
    /** @var \Wonderland\Application\Repository\RegionRepository **/
    protected $repository;
    
    public function setUp() {
        $this->loadFixture('countries');
        $this->loadFixture('regions');
        
        $this->repository = new RegionRepository($this->getConnection());
    }
    
    public function testFindRegionsByCountry() {
        $regions = $this->repository->findRegionsByCountry(1);
        
        $this->assertCount(2, $regions);
        $this->assertEquals([
            [
                'id' => '1',
                'country_id' => '1',
                'country_code' => 'fr',
                'country_label' => 'countries.france',
                'name' => 'Île-de-France',
                'latitude' => '75.366',
                'longitude' => '125.84',
                'created_at' => '2010-04-17 15:20:36',
            ],
            [
                'id' => '2',
                'country_id' => '1',
                'country_code' => 'fr',
                'country_label' => 'countries.france',
                'name' => 'Normandie',
                'latitude' => '63.465',
                'longitude' => '113.252',
                'created_at' => '2011-01-03 09:50:10'
            ]
        ], $regions);
    }
    
    public function testFind() {
        $region = $this->repository->find(1);
        
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Region', $region);
        $this->assertEquals(1, $region->getId());
        $this->assertEquals('Île-de-France', $region->getName());
        $this->assertEquals(75.366, $region->getLatitude());
        $this->assertEquals(125.84, $region->getLongitude());
        $this->assertInstanceOf('DateTime', $region->getCreatedAt());
        
        $country = $region->getCountry();
        $this->assertInstanceOf('Wonderland\\Application\\Model\\Country', $country);
        $this->assertEquals(1, $country->getId());
        $this->assertEquals('fr', $country->getCode());
        $this->assertEquals('countries.france', $country->getLabel());
    }
}