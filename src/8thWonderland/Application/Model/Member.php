<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Auth;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Database\Mysqli;

class Member {
    protected static $_instance;                // Instance unique de la classe
    protected $_id;                             // Identifiant de l'utilisateur
    protected $_properties = array();           // Liste des champs dans la table

    public function __construct($id)
    {
        if (isset($id) && !empty($id))      {   $this->_id = $id; return $this;   }

        $auth = Auth::getInstance();
        $this->_id = $auth->_getIdentity();

        $db = Registry::get('db');
        
        $this->_properties = $db->getColumns("Utilisateurs");
    }


    // Mise en place du singleton
    // ==========================
    public static function getInstance($id=null)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($id);
        }
        return self::$_instance;
    }


    // Renvoi de la valeur d'une propriété
    // ===================================
    public function __get($property)
    {
        $prop = strtolower($property);
        if (in_array($prop, $this->_properties)) {
            $method = "get" . ucfirst($prop);
            return $this->$method();
        }
        return null;
    }
        
    
    // Renvoi du login du membre
    // =========================
    protected function getLogin()
    {
        $db = Registry::get('db');
        $req = "SELECT Login " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Login'];
    }
    public function setLogin($value)
    {
        $value = htmlentities(trim($value));
        if ($this->getLogin() != $value) {
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Login='" . $value . "' WHERE IDUser=" . $this->_id;
            $res = $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update du login (" . $this->identite . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    protected function getPassword()
    {
        $db = Registry::get('db');
        $req = "SELECT Password " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Password'];
    }
    public function setPassword($value)
    {
        $value = hash("sha512", $value);
        if ($this->getPassword() != $value) {
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Password='" . $value . "' WHERE IDUser=" . $this->_id;
            $res = $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update du password (" . $this->identite . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    // Renvoi de l'identite du membre
    // ==============================
    protected function getIdentite()
    {
        $db = Registry::get('db');
        $req = "SELECT Identite " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Identite'];
    }
    public function setIdentite($value)
    {
        $value = htmlentities(trim($value));
        $old_identite = $this->getIdentite();
        if ($old_identite != $value) {
            $db = Registry::get('db');
            if (!self::ctrlIdentity($value)) {   return -1;  }
            if ($db->count("Utilisateurs", " WHERE Identite='" . $value . "'") > 0) {   return -2;  }

            $req = "UPDATE Utilisateurs SET Identite='" . $value . "' WHERE IDUser=" . $this->_id;
            $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'identite (" . $this->getIdentite() . ")", Log::ERR);
            } else {
                $req = "UPDATE phpbb_users SET username='" . $value . "' WHERE username='" . $old_identite . "'";
                $db->query($req);
                if ($db->affected_rows == 0) {
                    // log d'échec de mise à jour
                    $db_log = new Log("db");
                    $db_log->log("Echec de l'update de l'identite du forum (" . $value . ")", Log::ERR);
                }
            }

            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    // Renvoi de l'avatar du membre
    // ==============================
    protected function getAvatar()
    {
        $db = Registry::get('db');
        $req = "SELECT Avatar " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Avatar'];
    }
    public function setAvatar($value)
    {
        if ($this->getAvatar() != $value) {
            if(!filter_var($value, FILTER_VALIDATE_URL))    {   return -1;  }
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Avatar='" . $value . "' WHERE IDUser=" . $this->_id;
            $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'avatar (" . $this->getIdentite() . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    // Renvoi du mail du membre
    // ========================
    protected function getEmail()
    {
        $db = Registry::get('db');
        $req = "SELECT Email " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Email'];
    }
    public function setEmail($value)
    {
        if ($this->getEmail() != $value) {
            if (!self::ctrlMail($value)) {   return -1;  }
            $db = Registry::get('db');
            if ($db->count("Utilisateurs", " WHERE Email='" . $value . "'") > 0) {   return -2;  }

            $req = "UPDATE Utilisateurs SET Email='" . $value . "' WHERE IDUser=" . $this->_id;
            $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'email (" . $this->getIdentite() . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    // Renvoi du genre du membre
    // =========================
    protected function getSexe()
    {
        $db = Registry::get('db');
        $req = "SELECT Sexe " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Sexe'];
    }
    public function setSexe($value)
    {
        if (intval($value) != 1 && intval($value) != 2)     {   return -1;   }
        $value = intval($value)-1;
        if ($this->getSexe() != $value) {
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Sexe=" . $value . " WHERE IDUser=" . $this->_id;
            $res = $db->query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update du sexe (" . $this->identite . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    
    // Renvoi du code langue du membre
    // ===============================
    public function getLangue()
    {
        $db = Registry::get('db');
        $req = "SELECT Langue " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Langue'];
    }
    public function setLangue($value)
    {
        $opt = Registry::get("__options__");
        $langs = $opt['language']['langs'];
        $value = htmlentities($value);
        if ($this->getLangue() != $value) {
            if (array_key_exists($value, $langs)) {
                $db = Registry::getInstance();
                $req = "UPDATE Utilisateurs SET Langue='" . $value . "' WHERE IDUser=" . $this->_id;
                $db->query($req);
                if ($db->affected_rows == 0) {
                    // log d'échec de mise à jour
                    $db_log = new Log("db");
                    $db_log->log("Echec de l'update de la langue (" . $this->identite . ")", Log::ERR);
                } else
                {
                    $translate = Registry::get("translate");
                    $translate->setUserLang($value);
                }
                return $db->affected_rows;
            } else {    return 0;   }
        } else {    return 1;   }
    }
            
    
    // Renvoi du code pays du membre
    // =============================
    public function getPays()
    {
        $db = Registry::get('db');
        $req = "SELECT Pays " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Pays'];
    }
                
    
    // Renvoi du code REGION du membre
    // ===============================
    public function getRegion()
    {
        $db = Registry::get('db');
        $req = "SELECT Region " .
               "FROM Utilisateurs " .
               "WHERE IDUser = " . $this->_id . " " .
               "LIMIT 1";
        $res = $db->select($req);
        return $res[0]['Region'];
    }
    
    
    // Controle de l'identité
    // ======================
    public static function ctrlIdentity($identity)
    {
        $res = '';
        preg_match("/^[a-zA-Z][a-zA-Z0-9 _-]+/", $identity, $res);

        if (!$res || $res[0] != $identity)	{   return false;   }
        if (intval($identity) != 0)             {   return false;   }
        if (strlen($identity) < 3)              {   return false;   }
        
        return true;
    }
    
    
    // Controle de l'existence du mail
    // ===============================
    public static function ctrlMail($email)
    {
        $res = ''; $Username = ''; $Domain =''; $MXHost = '';
        $Modele = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
        preg_match($Modele, $email, $res);
        
        if (!$res || $res[0] != $email)         {   return false;   }
        
        list($Username, $Domain) = explode("@",$email);
        if(!getmxrr($Domain, $MXHost))		{   return false;   }
        
        return true;
    }
    
    
    // Verification si un utilisateur appartient à un groupe
    // =====================================================
    public static function EstMembre($Group_id, $User_id=0)
    {
        $db = Registry::get('db');
        $id_user = $User_id;
        if ($id_user == 0) {
            $auth = Auth::getInstance();
            $id_user = $auth->_getIdentity();
        }

        return $db->count("Citizen_Groups", sprintf(" WHERE Citizen_id=%d AND Group_id=%d", $id_user, $Group_id));
    }
        
    
    // Verification si un utilisateur est contact de region
    // ====================================================
    public static function isContact($id_group = null)
    {
        $db = Registry::get('db');
        $auth = Auth::getInstance();
        $id_user = $auth->_getIdentity();
        
        if (!isset($id_group)) {
            $res = $db->count("Groups", " WHERE ID_Contact=" . $id_user);
        } else {
            $res = $db->count("Groups", " WHERE ID_Contact=" . $id_user . " AND Group_id=" . $id_group);
        }
        
        return ($res);
    }

    
    // Renvoi du nombre total d'inscrits
    // =================================
    public static function Nb_Members()
    {
        $db = Registry::get('db');
        return $db->count("Utilisateurs");
    }
    
    
    // Renvoi du nombre d'actifs (dernière connexion < 3 semaines)
    // ===========================================================
    public static function Nb_ActivesMembers()
    {
        $db = Registry::get('db');
        return $db->count("Utilisateurs", " WHERE DATEDIFF(CURDATE(), DerConnexion) <21");
    }
    
    
    // Renvoi de la liste des pays
    // ===========================
    public function listCountries()
    {
        $db = Registry::get('db');
        $code_lang = $this->langue;
        if ($db->columnExists($code_lang, "country") == 0)       {   $code_lang = "en";  }

        $req = "SELECT Code, " . $code_lang . " " .
               "FROM country";
        $res = $db->select($req);
        return $res;
    }
    
    
    // Renvoi de la liste des membres
    // ==============================
    public static function ListMembers($params)
    {
        $db = Registry::get('db');
        $search = "";
        $table = "Utilisateurs";
        if (isset($params['sel_groups']) && !empty($params['sel_groups'])) {
            $table = "Citizen_Groups, Utilisateurs";
            $search = " WHERE Citizen_id=IDUser AND Group_id=" . $params['sel_groups'] . " ";
        }
        
        $req = "SELECT iduser, avatar, identite, sexe, email, langue, pays, region, derconnexion, inscription " .
               "FROM " . $table . " " . $search .
               "ORDER BY identite ASC";
        
        return $db->select($req);
    }
    
    
    // Renvoi la liste des Contacts de Groupe
    // ======================================
    public static function ListContactsGroups()
    {
        $db = Registry::get('db');
        $req = "SELECT IDUser, Group_name, Identite " .
               "FROM Utilisateurs, Groups WHERE IDUser=ID_Contact " .
               "ORDER BY Group_name ASC";
        
        return $db->select($req);
    }
}