<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Admin\Log;

/**
 * class myovh
 *
 * Gestion des informations et opérations liées à l'hébergeur OVH (serveur Mutualisé)
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 */
class Ovh
{
    private $_login;                                            // nichandle fourni par ovh
    private $_pwd;                                              // mot de passe fourni par ovh
    private $_multisession = false;                             // si TRUE, possibilité de se connecter plusieurs fois en même temps
    private $_langs = array("de", "en", "es", "fr", "it");      // liste des codes des langues gérées par SOAP chez OVH
    private $_ovh;                                              // identifiant de la connexion SOAP
    private $_soap;                                             // instance de la wdsl
    private $_domain = "8thwonderland.com";                     // nom du domaine à gérer
    private $_path;                                             // chemin du depot des scripts
    
    
    public function __construct() {
        $opt = Registry::get("__options__");
        $cfg_ovh = $opt['server'];
        $this->_login = strtolower($cfg_ovh['login']);
        if (substr($this->_login, -4) != "-ovh")    {   $this->_login = $this->_login . "-ovh"; }
        $this->_pwd = $cfg_ovh['pwd'];
        $this->_path = $cfg_ovh['crons_path'];
        $this->_soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.31.wsdl");
    }
    
    
    // Connexion au serveur OVH
    // ========================
    protected function connect()
    {
        $member = Member::getInstance();
        $lang_member = $member->Langue;
        $lang = "en";
        if (in_array($lang_member, $this->_langs))      {   $lang = $lang_member;   }
        
        try {
            $this->_ovh = $this->_soap->login($this->_login, $this->_pwd, $lang, $this->_multisession);
            return true;
        
        } catch (SoapFault $fault) {
            // Journal de log
            $db_log = new Log("db");
            $db_log->log("Echec de la connexion SOAP par " . $member->identite . "<br/>" . $fault, Log::WARN);
            return false;
        }
    }
    
    
    // Quotas du domaine
    // =================
    public function domain_quotas()
    {
        if ($this->connect()) {
            return $this->_soap->overquotaInfo($this->_ovh, $this->_domain);
            
            $this->disconnect();
        } else {
            return null;
        }
    }
    
    
    // Déconnexion au serveur OVH
    // ==========================
    protected function disconnect()
    {
        try {
            $this->_ovh = $this->_soap->logout($this->_ovh);
            return true;
        
        } catch (SoapFault $fault) {
            // Journal de log
            $member = Member::getInstance();
            $db_log = new Log("db");
            $db_log->log("Echec de la deconnexion SOAP par " . $member->identite . "<br/>" . $fault, Log::WARN);
            return false;
        }
    }
    
    
    // Liste des taches CRON
    // =====================
    public function list_cron()
    {
        if ($this->connect()) {
            return $this->_soap->crontabList($this->_ovh, $this->_domain);
            
            $this->disconnect();
        } else {
            return null;
        }
    }
    
    
    // Ajout d'une tache CRON
    // ======================
    public function add_cron($params)
    {
        if ($this->connect()) {
            $res = false;
            $member = Member::getInstance();
            $db_log = new Log("db");
            
            $desc = htmlentities($params['cron_desc']);
            // controle du script
            if ($params['cron_file'][0] != "/")     {   $params['cron_file'] = "/" . $params['cron_file'];    }
            $path = $this->_path . $params['cron_file'];
            if (!file_exists(substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT'])-3) . $path))    {   return -1;  }
            // controle des jours
            if ($params['cron_day'] == "all_days") {
                $weekday = ""; $days = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";
            } else {
                $weekday = $params['cron_day']; $days = "";
            }
            // controle des heures
            $hours = "";
            $keys = array_keys($params);
            for ($i=0; $i<count($keys); $i++) {
                if (substr($keys[$i], 0, 8) == "dayHour_" && $keys[$i] != "dayHour_all") {
                    $hours .= substr($keys[$i], 8) . ","; 
                }
            }
            if (strlen($hours) >0)      {   $hours = substr($hours, 0, strlen($hours)-1);   }
            
            
            try {
                $res = $this->_soap->crontabAdd($this->_ovh, $this->_domain, $path, "php5_3", $weekday, $days, $hours, $desc, "no");
                
                // Journal de log
                $db_log->log("Ajout de la tache cron '" . $desc . "' (" . $res . ") par " . $member->identite, Log::INFO);
                
            } catch (SoapFault $fault) {
                // Journal de log
                $db_log->log("Echec de l'ajout de la tache cron '" . $desc . "' par " . $member->identite . "<br/>" . $fault, Log::ERR);
            }
            $this->disconnect();
            return $res;
        } else {
            return -2;
        }
    }
    
    
    // Suppression d'une tache CRON
    // ============================
    public function delete_cron($id, $desc)
    {
        if ($this->connect()) {
            $res = false;
            $member = Member::getInstance();
            $db_log = new Log("db");
            
            try {
                $this->_soap->crontabDel($this->_ovh, $this->_domain, $id);
                $res = true;
                
                // Journal de log
                $db_log->log("Suppression de la tache cron '" . $desc . "' (" . $id . ") par " . $member->identite, Log::INFO);
                
            } catch (SoapFault $fault) {
                // Journal de log
                $db_log->log("Echec de la suppression de la tache cron '" . $desc . "' (" . $id . ") par " . $member->identite . "<br/>" . $fault, Log::ERR);
            }
            $this->disconnect();
            return $res;
        } else {
            return null;
        }
    }
}
?>
