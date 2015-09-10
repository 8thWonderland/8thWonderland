<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;

class MemberManager {
    /**
     * @param int $id
     * @return \Wonderland\Application\Model\Member
     */
    public function getMember($id) {
        $data = Registry::get('db')->select(
            'SELECT Login, Identite, Password, Email, Avatar, Sexe, Pays, Region, '.
            "DerConnexion, Inscription, isBlocked, Theme, Ip FROM Utilisateurs WHERE IDUser = $id"
        )[0];
        return
            (new Member())
            ->setLogin($data['Login'])
            ->setIdentity($data['Identite'])
            ->setPassword($data['Password'])
            ->setEmail($data['Email'])
            ->setAvatar($data['Avatar'])
            ->setGender($data['Sexe'])
            ->setCountry($data['Pays'])
            ->setRegion($data['Region'])
            ->setCreatedAt(new \DateTime($data['Inscription']))
            ->setLastConnectedAt(new \DateTime($data['DerConnexion']))
            ->setIsBanned($data['isBlocked'])
            ->setTheme($data['Theme'])
        ;
    }
    
    public function setLogin($value)
    {
        $value = htmlentities(trim($value));
        if ($this->getLogin() != $value) {
            $db = Registry::get('db');
            $db->_query(
                "UPDATE Utilisateurs SET Login='$value' WHERE IDUser={$this->id}"
            );
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log('db');
                $db_log->log("Echec de l'update du login ({$this->identite})", Log::ERR);
            }
            return $db->affected_rows;
        }
        return 1;
    }
    
    public function setPassword($value)
    {
        $value = hash('sha512', $value);
        if ($this->getPassword() != $value) {
            $db = Registry::get('db');
            $db->_query(
                "UPDATE Utilisateurs SET Password='$value' WHERE IDUser={$this->id}"
            );
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update du password ({$this->identite})", Log::ERR);
            }
            return $db->affected_rows;
        }
        return 1;
    }
    
    public function setIdentite($value)
    {
        $value = htmlentities(trim($value));
        $old_identite = $this->getIdentite();
        if ($old_identite != $value) {
            $db = Registry::get('db');
            if (!self::ctrlIdentity($value)) {   return -1;  }
            if ($db->count("Utilisateurs", " WHERE Identite='" . $value . "'") > 0) {   return -2;  }

            $req = "UPDATE Utilisateurs SET Identite='" . $value . "' WHERE IDUser=" . $this->id;
            $db->_query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'identite (" . $this->getIdentite() . ")", Log::ERR);
            } else {
                $req = "UPDATE phpbb_users SET username='" . $value . "' WHERE username='" . $old_identite . "'";
                $db->_query($req);
                if ($db->affected_rows == 0) {
                    // log d'échec de mise à jour
                    $db_log = new Log("db");
                    $db_log->log("Echec de l'update de l'identite du forum (" . $value . ")", Log::ERR);
                }
            }
            return $db->affected_rows;
        }
        return 1;
    }
    
    public function setAvatar($value)
    {
        if ($this->getAvatar() != $value) {
            if(!filter_var($value, FILTER_VALIDATE_URL))    {   return -1;  }
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Avatar='" . $value . "' WHERE IDUser=" . $this->id;
            $db->_query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'avatar (" . $this->getIdentite() . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    public function setEmail($value)
    {
        if ($this->getEmail() != $value) {
            if (!self::ctrlMail($value)) {   return -1;  }
            $db = Registry::get('db');
            if ($db->count("Utilisateurs", " WHERE Email='" . $value . "'") > 0) {   return -2;  }

            $req = "UPDATE Utilisateurs SET Email='" . $value . "' WHERE IDUser=" . $this->id;
            $db->_query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update de l'email (" . $this->getIdentite() . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    public function setSexe($value)
    {
        if (intval($value) != 1 && intval($value) != 2)     {   return -1;   }
        $value = intval($value)-1;
        if ($this->getSexe() != $value) {
            $db = Registry::get('db');
            $req = "UPDATE Utilisateurs SET Sexe=" . $value . " WHERE IDUser=" . $this->id;
            $res = $db->_query($req);
            if ($db->affected_rows == 0) {
                // log d'échec de mise à jour
                $db_log = new Log("db");
                $db_log->log("Echec de l'update du sexe (" . $this->identite . ")", Log::ERR);
            }
            return $db->affected_rows;
        } else {    return 1;   }
    }
    
    public function setLangue($value)
    {
        $opt = Registry::get("__options__");
        $langs = $opt['language']['langs'];
        $value = htmlentities($value);
        if ($this->getLangue() != $value) {
            if (array_key_exists($value, $langs)) {
                $db = Registry::getInstance();
                $req = "UPDATE Utilisateurs SET Langue='" . $value . "' WHERE IDUser=" . $this->id;
                $db->_query($req);
                if ($db->affected_rows == 0) {
                    // log d'échec de mise à jour
                    $db_log = new Log("db");
                    $db_log->log("Echec de l'update de la langue (" . $this->identite . ")", Log::ERR);
                } else
                {
                    $translate = Registry::get("translate");
                    $translate->setLangUser($value);
                }
                return $db->affected_rows;
            } else {    return 0;   }
        } else {    return 1;   }
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
        if ($db->ExistColumn($code_lang, "country") == 0)       {   $code_lang = "en";  }

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