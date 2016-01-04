<?php

namespace Wonderland\Application\Repository;

use Wonderland\Application\Model\Country;

class CountryRepository extends AbstractRepository
{
    /**
     * @return array
     */
    public function getCountries()
    {
        return $this->connection->query(
            'SELECT id, code, label FROM countries'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param type $id
     *
     * @return \Wonderland\Application\Model\Country | null
     */
    public function find($id)
    {
        $data = $this->connection->prepareStatement(
            'SELECT id, code, label FROM countries WHERE id = :id', ['id' => $id])->fetch(\PDO::FETCH_ASSOC);

        if ($data !== false) {
            return $this->formatObject($data);
        }

        return;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function formatObject($data)
    {
        return
            (new Country())
            ->setId($data['id'])
            ->setCode($data['code'])
            ->setLabel($data['label'])
        ;
    }
}
