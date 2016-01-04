<?php

namespace Wonderland\Application\Manager;

use Wonderland\Library\Application;

class TaskManager
{
    /** @var \Wonderland\Library\Application **/
    protected $application;

    /**
     * @param \Wonderland\Library\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param int $groupId
     *
     * @return array
     */
    public function getCurrentTasks($groupId)
    {
        return $this->application->get('database_connection')->query(
            'SELECT idtask, description, date, identite FROM tasks, Utilisateurs '.
            "WHERE status = 0 AND id_group = $groupId AND author = iduser ".
            'ORDER BY date DESC'
        );
    }

    public function getTask($id)
    {
        return $this->application->get('database_connection')->select(
            "SELECT idtask, description, date, identite FROM tasks, Utilisateurs WHERE author = iduser AND idtask = $id"
        );
    }

    /**
     * @param type   $description
     * @param string $date
     * @param type   $groupId
     *
     * @return type
     */
    public function createTask($description, $date, $groupId)
    {
        if (empty($description)) {
            return -1;
        }
        if (empty($date)) {
            $date = '0000-00-00 00:00:00';
        }
        $author = $this->application->get('auth')->getIdentity();
        $db = $this->application->get('database_connection');
        $db->query(
            'INSERT INTO tasks (Description, date, id_group, status, author) '.
            "VALUES ('".htmlentities($description)."', '$date', $groupId, 0, $author)"
        );

        return $db->affected_rows;
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function deleteTask($id)
    {
        $db = $this->application->get('database_connection');
        $db->query("DELETE FROM tasks WHERE IDTask = $id");

        return $db->affected_rows;
    }
}
