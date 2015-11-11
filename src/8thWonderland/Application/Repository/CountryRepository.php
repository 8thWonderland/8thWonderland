<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Country;

class CountryRepository extends AbstractRepository {
    /**
     * @return array
     */
    public function getCountries() {
        return $this->connection->query(
            'SELECT id, code, label FROM countries'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @param array $data
     * @return array
     */
    public function formatObject($data) {
        return
            (new Country())
            ->setId($data['id'])
            ->setCode($data['code'])
            ->setLabel($data['label'])
        ;
    }
}