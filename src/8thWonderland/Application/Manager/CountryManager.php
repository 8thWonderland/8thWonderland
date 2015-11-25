<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Repository\CountryRepository;

class CountryManager {
    /** @var \Wonderland\Application\Repository\CountryRepository **/
    protected $repository;
    
    /**
     * @param \Wonderland\Application\Repository\CountryRepository $repository
     */
    public function __construct(CountryRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * @return array
     */
    public function getCountries() {
        return $this->repository->getCountries();
    }
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Country
     */
    public function get($id) {
        return $this->repository->find($id);
    }
}