<?php

/**
 * Class memory_registry
 *
 *  Utilisation de l'abstraction pour piloter les sessions PHP
 *
 */


class memory_registry {
    
    public static function start($options = null)
    {
        // Initialisation des options
        if (isset($options))                        {   $this->setOptions($options);                        }
        
        
        // Démarre la session
        return session_start();
    }
    
    
    // Initialisation des options
    // ==========================
    public function setOptions($options)
    {
        if (!is_array($options)) {      throw new Exception('Invalid options provider : This must be an array !');      }
        $options = array_change_key_case($options, CASE_LOWER);
        
        if (!empty($options['session_name']) && is_string($options['session_name']))                {   session_name($options['session_name']);  }
        if (!empty($options['session_cache_expire']) && is_int($options['session_cache_expire']))   {   session_cache_expire($options['session_cache_expire']);  }
    }
    
    
    public static function get($key)
    {
        if (isset($_SESSION[$key]))     {   return $_SESSION[$key];     }
        return null;
    }
    public static function set($key, $value)
    {
        $_SESSION[$key]=$value;
    }
    public static function delete($key)
    {
        if ($key != "__identity__")     {   unset($_SESSION[$key]);     }
        else {
            $_SESSION = array();
            session_destroy();
        }
    }
    
    
    // Récupère le session_id
    // ======================
    public static function get_ID()
    {
        return session_id();
    }
    
    
    // Récupère le délai d'expiration du cache
    // =======================================
    public static function get_CacheExpire()
    {
        return session_cache_expire();
    }
}

?>
