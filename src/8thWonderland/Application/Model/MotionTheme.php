<?php

namespace Wonderland\Application\Model;

class MotionTheme {
    /** @var int **/
    protected $id;
    /** @var string **/
    protected $name;
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
     * @param string $name
     * @return \Wonderland\Application\Model\MotionTheme
     */
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
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