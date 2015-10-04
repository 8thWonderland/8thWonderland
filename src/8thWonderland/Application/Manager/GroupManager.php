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
            'SELECT g.Group_id, g.Group_name, g.Description, u.identity, g.Creation, gt.Group_Type_Description ' .
            'FROM Groups g ' .
            'INNER JOIN Group_Types gt ON gt.Group_Type_Id = g.Group_Type ' .
            'INNER JOIN users u ON u.id = g.ID_Contact ' .
            'ORDER BY g.Group_name ASC'
        );
    }
    
    /**
     * @return array
     */
    public function getRegionalGroups() {
        return $this->connection->select(
            'SELECT g.Group_id, g.Group_name, g.Description, u.identity, g.Creation, gt.Group_Type_Description, r.Longitude, r.Latitude ' .
            'FROM Groups g ' .
            'INNER JOIN Group_Types gt ON gt.Group_Type_Id = g.Group_Type ' .
            'INNER JOIN users u ON u.id = g.ID_Contact ' .
            'INNER JOIN regions r ON r.Name = g.Group_name ' .
            'WHERE r.Longitude IS NOT NULL AND r.Latitude IS NOT NULL ' .
            'ORDER BY g.Group_name ASC'
        );
    }
    
    /**
     * @param int $memberId
     * @return array
     */
    public function getMemberGroups($memberId) {
        return $this->connection->query(
            'SELECT DISTINCT g.Group_id, g.Group_name ' .
            'FROM Groups g ' .
            'INNER JOIN Citizen_Groups cg ON g.Group_id = cg.Group_id ' .
            "WHERE cg.Citizen_id = $memberId OR g.ID_Contact = $memberId " .
            'ORDER BY g.Group_name ASC'
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
            'SELECT u.id, u.identity, u.last_connected_at ' .
            'FROM Citizen_Groups cg ' .
            'INNER JOIN users u ON cg.Citizen_id = u.id ' .
            "WHERE cg.Group_id = $groupId " .
            'ORDER BY u.identity ASC'
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