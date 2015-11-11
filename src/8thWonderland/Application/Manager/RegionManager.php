<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Repository\RegionRepository;

class RegionManager {
    /** @var \Wonderland\Application\Repository\RegionRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Application\Repository\RegionRepository $repository
     */
    public function __construct(RegionRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * @param int $country
     * @return array
     */
    public function getRegionsByCountry($countryId) {
        return $this->repository->findRegionsByCountry($countryId);
    }
}