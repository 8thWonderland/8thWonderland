<?php

namespace Wonderland\Application\Model;

class Country
{
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $code;
    /** @var string **/
    protected $label;

    /**
     * @param int $id
     *
     * @return \Wonderland\Application\Model\Country
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
     * @param string $code
     *
     * @return \Wonderland\Application\Model\Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $label
     *
     * @return \Wonderland\Application\Model\Country
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
