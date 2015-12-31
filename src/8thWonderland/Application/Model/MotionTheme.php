<?php

namespace Wonderland\Application\Model;

class MotionTheme {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $label;
    /** @var int **/
    protected $duration;
    
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\MotionTheme
     */
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
     * @return \Wonderland\Application\Model\MotionTheme
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
    
    /**
     * @param int $duration
     * @return \Wonderland\Application\Model\MotionTheme
     */
    public function setDuration($duration) {
        $this->duration = $duration;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getDuration() {
        return $this->duration;
    }
}