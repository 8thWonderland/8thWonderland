<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Repository\RegionRepository;

class RegionManager
{
    /** @var \Wonderland\Application\Repository\RegionRepository **/
    protected $repository;

    /**
     * @param \Wonderland\Application\Repository\RegionRepository $repository
     */
    public function __construct(RegionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $countryId
     *
     * @return array
     */
    public function getRegionsByCountry($countryId)
    {
        return $this->repository->findRegionsByCountry($countryId);
    }

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }
}
