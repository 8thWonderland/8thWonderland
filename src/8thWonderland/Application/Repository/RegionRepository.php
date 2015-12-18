<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Country;
use Wonderland\Application\Model\Region;

class RegionRepository extends AbstractRepository {
    /**
     * @param int $countryId
     * @return array
     */
    public function findRegionsByCountry($countryId) {
        return $this->connection->prepareStatement(
            'SELECT r.id, r.country_id, c.code as country_code, c.label as country_label, ' .
            'r.name, r.latitude, r.longitude, r.created_at FROM regions r ' .
            'INNER JOIN countries c ON c.id = r.country_id ' .
            'WHERE r.country_id = :country_id'
        , ['country_id' => $countryId])->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * @param type $id
     * @return \Wonderland\Application\Model\Country | null
     */
    public function find($id) {
        $data = $this->connection->prepareStatement(
            'SELECT r.id, r.country_id, c.code as country_code, c.label as country_label, ' .
            'r.name, r.latitude, r.longitude, r.created_at FROM regions r ' .
            'INNER JOIN countries c ON c.id = r.country_id ' .
            'WHERE r.id = :id'
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
            ->setCountry(
                (new Country())
                ->setId($data['country_id'])
                ->setCode($data['country_code'])
                ->setLabel($data['country_label'])
            )
            ->setName($data['name'])
            ->setLongitude($data['longitude'])
            ->setLatitude($data['latitude'])
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }
}