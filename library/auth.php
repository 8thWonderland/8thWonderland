<?php

/**
 * Gestion des connexions/deconnexions des utilisateurs
 *
 * @author Brennan
 */


class auth {
    protected static $_instance;                // Instance unique de la classe
    protected $_tablename;                      // Nom de la table servant pour la liaison
    protected $_logincolumn;                    // Nom de la colonne dans la table servant pour le login
    protected $_pwdcolumn;                      // Nom de la colonne dans la table servant pour le password
    protected $_primarykey = "iduser";          // Clé primaire de la table des membres
        
    
    // Mise en place du singleton
    // ==========================
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    // Définition du nom de la table
    // =============================
    public function setTableName($nomtable)
    {
        $this->_tablename = $nomtable;
    }
        
    
    // Définition du nom de la colonne du login
    // ========================================
    public function setIdentityColumn($logincolumn)
    {
        $this->_logincolumn = $logincolumn;
    }
            
    
    // Définition du nom de la colonne du password
    // ===========================================
    public function setCredentialColumn($pwdcolumn)
    {
        $this->_pwdcolumn = $pwdcolumn;
    }
    
    
    // controle d'authentification
    // ===========================
    public function authenticate($login, $pwd)
    {
        $req = "SELECT " . $this->_primarykey . " FROM " . $this->_tablename . " " .
               "WHERE " . $this->_logincolumn . " = '" . $login . "' AND " . $this->_pwdcolumn . " = '" . $pwd . "'";
        
        $db = db_mysqli::getInstance();
        if ($res = $db->query($req))
        {
            if ($res->num_rows == 1)
            {
                $row = $res->fetch_assoc();
                $this->_setIdentity($row[$this->_primarykey]);
                return true;
            }
            
            return false;
        }
        else {
            if ($db->connect_errno)   {   throw new exception($this->_dbAdapter->connect_error);  }
        }
        
    }
    
    
    // Déconnexion de l'utilisateur
    // ============================
    public function logout()
    {
        memory_registry::delete("__identity__");
    }
    
    
    // Identité de l'utilisateur
    // =========================
    protected function _setIdentity($id)
    {
        memory_registry::set("__identity__", $id);
    }
    public function _getIdentity()
    {
        return memory_registry::get("__identity__");
    }
    
    
    // Test l'existence de l'identité
    // ==============================
    public static function hasIdentity()
    {
        return (memory_registry::get("__identity__") != null);
    }
}

?>
