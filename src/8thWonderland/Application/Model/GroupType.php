<?php

namespace Wonderland\Application\Model;

class GroupType {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $description;
    
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param string $description
     * @return \Wonderland\Application\Model\GroupType
     */
    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
}