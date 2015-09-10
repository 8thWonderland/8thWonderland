<?php

namespace Wonderland\Application\Model;

use Wonderland\Library\Memory\Registry;

use Wonderland\Library\Auth;

/**
 * class managegroups
 *
 * Gestion des informations des groupes
 *
 * @author Brennan
 */
class ManageGroups {
    
    // Affichage des groupes
    // =====================
    public static function display_groups()
    {
        $db = Registry::get('db');

        $req = "SELECT Group_id, Group_name, Description, Identite, Creation, Group_Type_Description " .
               "FROM Groups, Group_Types, Utilisateurs " .
               "WHERE ID_Contact=Utilisateurs.IDUser AND Group_Type=Group_Type_Id " .
               "ORDER BY Group_name ASC";

        return $db->select($req);
    }
    
    
    // Affichage des groupes
    // =====================
    public static function display_groups_regions()
    {
        $db = Registry::get('db');

        $req = "SELECT Group_id, Group_name, Description, Identite, Groups.Creation as 'Creation', Group_Type_Description, Longitude, Latitude " .
               "FROM Groups, Group_Types, Utilisateurs, regions " .
               "WHERE ID_Contact=Utilisateurs.IDUser AND Group_Type=Group_Type_Id AND regions.Name = Groups.Group_name AND regions.Longitude IS NOT NULL AND regions.Latitude IS NOT NULL " .
               "ORDER BY Group_name ASC";

        return $db->select($req);
    }
    
    
    // Affichage des groupes auxquels le membre appartient
    // ===================================================
    public static function display_groupsMember()
    {
        $auth = Auth::getInstance();
        $db = Registry::get('db');

        $req = "SELECT DISTINCT Groups.Group_id, Group_name " .
               "FROM Groups, Citizen_Groups " .
               "WHERE Groups.Group_id=Citizen_Groups.Group_id AND (Citizen_Groups.Citizen_id=" . $auth->getIdentity() . " OR ID_Contact=" . $auth->getIdentity() . ") " .
               "ORDER BY Group_name ASC";

        return $db->query($req);
    }
    
    
    // Nombre de membres dans le groupe
    // ================================
    public static function NbMembers($id_group)
    {
        if (!isset($id_group))  {   return 0;   }
        $db = Registry::get('db');
        return $db->count("Citizen_Groups", " WHERE Group_id=" . $id_group);
    }
    
    
    // Renvoies la liste des membres du groupe et leur derniere connexion
    // ==================================================================
    public static function display_listMembers()
    {
        $db = Registry::get('db');

        $req = "SELECT IDUser, Identite, DerConnexion " .
               "FROM Citizen_Groups, Utilisateurs " .
               "WHERE Citizen_id=IDUser AND Group_id=" . Registry::get("desktop") . " " .
               "ORDER BY Identite ASC";

        return $db->select($req);
    }
    
    
    // Renvoi la liste des membres du groupe pour choisir un Contact de groupe
    // =======================================================================
    public static function display_listMembersContact()
    {
        $db = Registry::get('db');

        $req = "SELECT DISTINCT IDUser, Identite " .
               "FROM Citizen_Groups, Utilisateurs " .
               "WHERE Citizen_id=IDUser AND Group_id=" . Registry::get("desktop") . " " .
               "ORDER BY Identite ASC";

        return $db->select($req);
    }

    
    // Changement du contact de groupe
    // ===============================
    public static function change_contact($idcontact)
    {
        $db = Registry::get('db');

        $req = "UPDATE Groups SET ID_Contact=" . $idcontact . " " .
               "WHERE Group_id=" . Registry::get("desktop") . " ";
        $db->query($req);
        
        return $db->affected_rows;
    }
}