<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Database\Mysqli;

class GroupManager {
    /** @var \Wonderland\Library\Database\Mysqli **/
    protected $connection;
    
    /**
     * @param \Wonderland\Library\Database\Mysqli $connection
     */
    public function __construct(Mysqli $connection) {
        $this->connection = $connection;
    }
    
    /**
     * @return array
     */
    public function getGroups() {
        return $this->connection->select(
            'SELECT Group_id, Group_name, Description, identity, Creation, Group_Type_Description ' .
            'FROM Groups, Group_Types, users ' .
            'WHERE ID_Contact = users.id AND Group_Type = Group_Type_Id ' .
            'ORDER BY Group_name ASC'
        );
    }
    
    /**
     * @return array
     */
    public function getRegionalGroups() {
        return $this->connection->select(
            'SELECT Group_id, Group_name, Description, identity, Groups.Creation as Creation, Group_Type_Description, Longitude, Latitude ' .
            'FROM Groups, Group_Types, users, regions ' .
            'WHERE ID_Contact = users.id AND Group_Type = Group_Type_Id AND regions.Name = Groups.Group_name AND regions.Longitude IS NOT NULL AND regions.Latitude IS NOT NULL ' .
            'ORDER BY Group_name ASC'
        );
    }
    
    /**
     * @param int $memberId
     * @return array
     */
    public function getMemberGroups($memberId) {
        return $this->connection->query(
            'SELECT DISTINCT Groups.Group_id, Group_name ' .
            'FROM Groups, Citizen_Groups ' .
            "WHERE Groups.Group_id=Citizen_Groups.Group_id AND (Citizen_Groups.Citizen_id = $memberId OR ID_Contact = $memberId) " .
            'ORDER BY Group_name ASC'
        );
    }
    
    /**
     * @param int $groupId
     * @return int
     */
    public function countMembers($groupId) {
        if (!isset($groupId)) {
            return 0;
        }
        return $this->connection->count('Citizen_Groups', " WHERE Group_id = $groupId");
    }
    
    /**
     * No idea sur l'utilité de cette fonction
     * 
     * @return array
     */
    public function getGroupMembers($groupId) {
        return $this->connection->select(
            'SELECT id, identity, last_connected_at ' .
            'FROM Citizen_Groups, users ' .
            "WHERE Citizen_id = id AND Group_id = $groupId " .
            'ORDER BY identity ASC'
        );
    }

    /**
     * Update Group's contact
     * 
     * @param int $groupId
     * @param int $contactId
     * @return int
     */
    public function updateContact($groupId, $contactId) {
        $this->connection->query("UPDATE Groups SET ID_Contact = $contactId WHERE Group_id = $groupId");
        return $this->connection->affected_rows;
    }
}