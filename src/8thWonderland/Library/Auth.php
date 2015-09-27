<?php

namespace Wonderland\Library;

use Wonderland\Library\Memory\Registry;

class Auth {
    /** @var \Wonderland\Library\Application **/
    protected $application;
    /** @var string **/
    protected $tableName;
    /** @var string **/
    protected $loginColumn;
    /** @var string **/
    protected $passwordColumn;
    /** @var string **/
    protected $primaryKey = 'iduser';
    
    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }
    
    /**
     * @param string $tableName
     * @return \Wonderland\Library\Auth
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        
        return $this;
    }
        
    /**
     * @param string $loginColumn
     * @return \Wonderland\Library\Auth
     */
    public function setIdentityColumn($loginColumn) {
        $this->loginColumn = $loginColumn;
        
        return $this;
    }
            
    /**
     * @param string $passwordColumn
     * @return \Wonderland\Library\Auth
     */
    public function setCredentialColumn($passwordColumn) {
        $this->passwordColumn = $passwordColumn;
        
        return $this;
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
            "SELECT {$this->primaryKey} FROM {$this->tableName} " .
            "WHERE {$this->loginColumn} = '$login' AND {$this->passwordColumn} = '$password'"
        );
        if ($res) {
            if ($res->num_rows === 1) {
                $this->setIdentity($res->fetch_assoc()[$this->primaryKey]);
                return true;
            }
            return false;
        } elseif ($db->connect_errno) {
            throw new exception($this->_dbAdapter->connect_error);
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