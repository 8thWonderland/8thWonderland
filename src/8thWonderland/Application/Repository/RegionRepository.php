<?php

namespace Wonderland\Application\Repository;

class RegionRepository extends AbstractRepository {
    /**
     * @param int $countryId
     * @return array
     */
    public function findRegionsByCountry($countryId) {
        return $this->connection->prepareStatement(
            'SELECT id, country_id, name, latitude, longitude, created_at FROM regions WHERE country_id = :country_id'
        , ['country_id' => $countryId])->fetchAll(\PDO::FETCH_ASSOC);
    }
}