<?php

/**
 * Mise en cache de certaines valeurs ; sérialisation dans un fichier
 *
 * @author Brennan
 */

class cache {
    private static $_cachePath = 'application/';                // Emplacement par défaut des fichiers de cache
    private static $_defaultLifetime = 3600;                    // Durée de vie par défaut de la valeur mise en cache
    private static $_suffix = ".cache.txt";                     // Suffixe de fichier : 'mon_cache' => 'mon_cache.cache.txt'
    private static $_separator = "*;;*";                        // Séparateur de valeurs dans les lignes
    
    
    // Récupère une valeur dans le cache
    // =================================
    public static function get($id)
    {
        $file = self::$_cachePath . session_id() . self::$_suffix;
        if(file_exists($file))
        {
            $cache = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($cache as $line) {
                $datas = explode(self::$_separator, $line);
                if ($datas[0] == $id && $datas[2]>time())   {   return unserialize($datas[1]);     }
            }
        }
        return null;
    }
    
    
    // Enregistre une valeur dans le cache
    // ===================================
    public static function set($id, $value, $lifetime = null)
    {
        $file = self::$_cachePath . session_id() . self::$_suffix;
        (isset($lifetime)?$timeclose=time()+$lifetime:$timeclose=time()+self::$_defaultLifetime);
        
        // Ajout de la valeur
        if (!self::isExist($id)) {
            $handle = fopen($file, "c");
            if (fseek($handle, filesize($file), SEEK_SET) == 0) {
                fwrite($handle, $id . self::$_separator . serialize($value) . self::$_separator . $timeclose . "\n");
            }
        }
        // Modification de la valeur
        else
        {
            $handle = fopen($file, "r+");
            $cache = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            for($i=0; $i<count($cache); $i++) {
                $datas = explode(self::$_separator, $cache[$i]);
                
                if ($datas[0] == $id) {
                    $cache[$i] = str_replace($datas[1], serialize($value), $cache[$i]);
                    break;
                }
            }
            fwrite($handle, implode(self::$_separator, $cache));
        }
        
    }
    
    
    // Vérifie si une valeur est dans le cache
    // =======================================
    private static function isExist($id)
    {
        $file = self::$_cachePath . session_id() . self::$_suffix;
        if(file_exists($file))
        {
            $cache = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($cache as $line) {
                $datas = explode(self::$_separator, $line);
                if ($datas[0] == $id && $datas[2]>time())   {   return true;     }
            }
        }
        return false;
    }
}

?>
