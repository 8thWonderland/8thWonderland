<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;

class Auth {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param string $login
     * @param string $password
     * @return boolean
     * @throws \Exception
     */
    public function authenticate($login, $password) {
        $db = $this->application->get('mysqli');
        $res = $db->query(
            "SELECT id FROM users " .
            "WHERE login = '$login' AND password = '$password'"
        );
        if ($res) {
            if ($res->num_rows === 1) {
                $this->setIdentity($res->fetch_assoc()['id']);
                return true;
            }
            return false;
        } elseif ($db->connect_errno) {
            throw new \Exception($this->_dbAdapter->connect_error);
        }
    }
    
    public function logout() {
        Registry::delete('__identity__');
    }
    
    /**
     * @param int $id
     */
    protected function setIdentity($id) {
        Registry::set('__identity__', $id);
    }
    
    /**
     * @return int
     */
    public function getIdentity() {
        return Registry::get('__identity__');
    }
    
    /**
     * @return boolean
     */
    public static function hasIdentity() {
        return (Registry::get('__identity__') !== null);
    }
}