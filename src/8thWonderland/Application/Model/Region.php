<?php

namespace Wonderland\Application\Model;

class Region
{
    /** @var int **/
    protected $id;
    /** @var \Wonderland\Application\Model\Country **/
    protected $country;
    /** @var string **/
    protected $name;
    /** @var float **/
    protected $latitude;
    /** @var float **/
    protected $longitude;
    /** @var \DateTime **/
    protected $createdAt;

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Wonderland\Application\Model\Country $country
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return \Wonderland\Application\Model\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $name
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param float $latitude
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $longitude
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \Wonderland\Application\Model\Region
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
