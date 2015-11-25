<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Region;

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
    
    /**
     * @param type $id
     * @return \Wonderland\Application\Model\Country | null
     */
    public function find($id) {
        $data = $this->connection->prepareStatement(
            'SELECT id, name, longitude, latitude, created_at FROM regions WHERE id = :id'
        , ['id' => $id])->fetch(\PDO::FETCH_ASSOC);
        
        if($data !== false) {
            return $this->formatObject($data);
        }
        return null;
    }
    
    /**
     * @param array $data
     * @return \Wonderland\Application\Model\Country
     */
    public function formatObject($data) {
        return
            (new Region())
            ->setId($data['id'])
            ->setName($data['name'])
            ->setLongitude($data['longitude'])
            ->setLatitude($data['latitude'])
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }
}