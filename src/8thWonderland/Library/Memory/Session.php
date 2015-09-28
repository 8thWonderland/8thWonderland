<?php

namespace Wonderland\Library\Memory;

use Wonderland\Library\Application;

class Session {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
        session_start();
    }
    
    /**
     * @param string $key
     * @return mixed|null
     */
    public function get($key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }
    
    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * @param string $key
     * @return boolean
     */
    public function delete($key) {
        if ($key !== '__identity__') {
            unset($_SESSION[$key]);
            return true;
        }
        session_destroy();
        return false;
    }
    
    /**
     * @return string
     */
    public function getId() {
        return session_id();
    }
    
    /**
     * @return int
     */
    public function getCacheExpire() {
        return session_cache_expire();
    }
}
