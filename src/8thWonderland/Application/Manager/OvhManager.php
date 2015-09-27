<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Application;

use Wonderland\Library\Admin\Log;

class OvhManager {
    /** @var Application **/
    protected $application;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @return boolean
     */
    protected function connect() {
        $memberManager = $this->application->get('member_manager');
        $member = $memberManager->getCurrentMember();
        $memberLanguage = $member->getLanguage();
        $lang =
            (in_array($memberLanguage, $this->_langs))
            ? $memberLanguage
            : 'en'
        ;
        
        try {
            $this->_ovh = $this->_soap->login($this->_login, $this->_pwd, $lang, $this->_multisession);
            return true;
        
        } catch (\SoapFault $fault) {
            // Journal de log
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec de la connexion SOAP par {$member->getIdentity()}<br/>" . $fault, Log::WARN);
            return false;
        }
    }
    
    /**
     * @return array|null
     */
    public function getDomainQuotas() {
        if ($this->connect()) {
            return $this->_soap->overquotaInfo($this->_ovh, $this->_domain);
        }
        return null;
    }
    
    /**
     * @return boolean
     */
    protected function disconnect() {
        try {
            $this->_ovh = $this->_soap->logout($this->_ovh);
            return true;
        
        } catch (SoapFault $fault) {
            // Journal de log
            $member = $this->application->get('member_manager')->getCurrentMember();
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            $logger->log("Echec de la deconnexion SOAP par {$member->getIdentity()}<br/>" . $fault, Log::WARN);
            return false;
        }
    }
    
    /**
     * @return array|null
     */
    public function getCrons()
    {
        if ($this->connect()) {
            return $this->_soap->crontabList($this->_ovh, $this->_domain);
        }
        return null;
    }
    
    /**
     * @param array $params
     * @return int
     */
    public function addCron($params) {
        if ($this->connect()) {
            $res = false;
            $member = $this->application->get('member_manager')->getCurrentMember();
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            
            $desc = htmlentities($params['cron_desc']);
            // controle du script
            if ($params['cron_file'][0] !== '/') {
                $params['cron_file'] = '/' . $params['cron_file'];
            }
            $path = $this->_path . $params['cron_file'];
            if (!file_exists(substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT'])-3) . $path)) {
                return -1;
            }
            // controle des jours
            if ($params['cron_day'] === 'all_days') {
                $weekday = '';
                $days = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31';
            } else {
                $weekday = $params['cron_day'];
                $days = '';
            }
            // controle des heures
            $hours = '';
            $keys = array_keys($params);
            $nbKeys = count($keys);
            for ($i = 0; $i < $nbKeys; ++$i) {
                if (substr($keys[$i], 0, 8) === 'dayHour_' && $keys[$i] !== 'dayHour_all') {
                    $hours .= substr($keys[$i], 8) . ','; 
                }
            }
            if (strlen($hours) > 0) {
                $hours = substr($hours, 0, strlen($hours)-1);
            }
            
            try {
                $res = $this->_soap->crontabAdd($this->_ovh, $this->_domain, $path, 'php5_3', $weekday, $days, $hours, $desc, 'no');
                // Journal de log
                $logger->log("Ajout de la tache cron '$desc' ($res) par {$member->getIdentity()}", Log::INFO);
                
            } catch (SoapFault $fault) {
                // Journal de log
                $logger->log("Echec de l'ajout de la tache cron '$desc' par {$member->getIdentity()}<br/>$fault", Log::ERR);
            }
            $this->disconnect();
            return $res;
        } else {
            return -2;
        }
    }
    
    /**
     * @param int $id
     * @param string $desc
     * @return boolean|null
     */
    public function deleteCron($id, $desc) {
        if ($this->connect()) {
            $res = false;
            $member = $this->application->get('member_manager')->getCurrentMember();
            $logger = $this->application->get('logger');
            $logger->setWriter('db');
            
            try {
                $this->_soap->crontabDel($this->_ovh, $this->_domain, $id);
                $res = true;
                // Journal de log
                $logger->log("Suppression de la tache cron '$desc' ($id) par {$member->getIdentity()}", Log::INFO);
                
            } catch (SoapFault $fault) {
                // Journal de log
                $logger->log("Echec de la suppression de la tache cron '$desc' ($id) par {$member->getIdentity()}<br/>$fault", Log::ERR);
            }
            $this->disconnect();
            return $res;
        }
        return null;
    }
}