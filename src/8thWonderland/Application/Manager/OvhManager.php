<?php

namespace Wonderland\Application\Manager;

use Wonderland\Application\Model\Member;
use Wonderland\Library\Admin\Log;

class OvhManager
{
    /** @var \Wonderland\Library\Admin\Log **/
    protected $logger;

    /**
     * @param \Wonderland\Library\Admin\Log $logger
     */
    public function __construct(Log $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Wonderland\Application\Model\Member $member
     *
     * @return bool
     */
    protected function connect(Member $member)
    {
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
            $logger->log("Echec de la connexion SOAP par {$member->getIdentity()}<br/>".$fault, Log::WARN);

            return false;
        }
    }

    /**
     * @param \Wonderland\Application\Model\Member
     *
     * @return array|null
     */
    public function getDomainQuotas(Member $member)
    {
        if ($this->connect($member)) {
            return $this->_soap->overquotaInfo($this->_ovh, $this->_domain);
        }

        return;
    }

    /**
     * @param \Wonderland\Application\Model\Member $member
     *
     * @return bool
     */
    protected function disconnect(Member $member)
    {
        try {
            $this->_ovh = $this->_soap->logout($this->_ovh);

            return true;
        } catch (SoapFault $fault) {
            // Journal de log
            $this->logger->setWriter('db');
            $this->logger->log("Echec de la deconnexion SOAP par {$member->getIdentity()}<br/>".$fault, Log::WARN);

            return false;
        }
    }

    /**
     * @param \Wonderland\Application\Model\Member $member
     *
     * @return array|null
     */
    public function getCrons(Member $member)
    {
        if ($this->connect($member)) {
            return $this->_soap->crontabList($this->_ovh, $this->_domain);
        }

        return;
    }

    /**
     * @param \Wonderland\Application\Model\Member
     * @param array $params
     *
     * @return int
     */
    public function addCron(Member $member, $params)
    {
        if ($this->connect($member)) {
            $res = false;
            $this->logger->setWriter('db');

            $desc = htmlentities($params['cron_desc']);
            // controle du script
            if ($params['cron_file'][0] !== '/') {
                $params['cron_file'] = '/'.$params['cron_file'];
            }
            $path = $this->_path.$params['cron_file'];
            if (!file_exists(substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT']) - 3).$path)) {
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
                    $hours .= substr($keys[$i], 8).',';
                }
            }
            if (strlen($hours) > 0) {
                $hours = substr($hours, 0, strlen($hours) - 1);
            }

            try {
                $res = $this->_soap->crontabAdd($this->_ovh, $this->_domain, $path, 'php5_3', $weekday, $days, $hours, $desc, 'no');
                // Journal de log
                $this->logger->log("Ajout de la tache cron '$desc' ($res) par {$member->getIdentity()}", Log::INFO);
            } catch (SoapFault $fault) {
                // Journal de log
                $this->logger->log("Echec de l'ajout de la tache cron '$desc' par {$member->getIdentity()}<br/>$fault", Log::ERR);
            }
            $this->disconnect($member);

            return $res;
        } else {
            return -2;
        }
    }

    /**
     * @param \Wonderland\Application\Model\Member $member
     * @param int                                  $id
     * @param string                               $desc
     *
     * @return bool|null
     */
    public function deleteCron(Member $member, $id, $desc)
    {
        if ($this->connect($member)) {
            $res = false;
            $this->logger->setWriter('db');

            try {
                $this->_soap->crontabDel($this->_ovh, $this->_domain, $id);
                $res = true;
                // Journal de log
                $logger->log("Suppression de la tache cron '$desc' ($id) par {$member->getIdentity()}", Log::INFO);
            } catch (SoapFault $fault) {
                // Journal de log
                $logger->log("Echec de la suppression de la tache cron '$desc' ($id) par {$member->getIdentity()}<br/>$fault", Log::ERR);
            }
            $this->disconnect($member);

            return $res;
        }

        return;
    }
}
