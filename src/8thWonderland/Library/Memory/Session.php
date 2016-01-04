<?php

namespace Wonderland\Library\Memory;

class Session
{
    public function __construct()
    {
        session_start();
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
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
    public function getId()
    {
        return session_id();
    }

    /**
     * @return int
     */
    public function getCacheExpire()
    {
        return session_cache_expire();
    }
}
