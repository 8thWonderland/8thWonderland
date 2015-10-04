<?php

namespace Wonderland\Application\Model;

class GroupType {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $label;
    
    public function setId($id) {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @param string $label
     * @return \Wonderland\Application\Model\GroupType
     */
    public function setLabel($label) {
        $this->label = $label;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }
}