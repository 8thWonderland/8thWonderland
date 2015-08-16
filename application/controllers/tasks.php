<?php

/**
 * Controleur des taches de groupes
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/


class tasks extends controllers_action {

    public function display_createtaskAction()
    {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->_view['id_group'] = $_POST['id_group'];
        $this->render('tasks/create_task');
    }
    
    
    public function valid_taskAction()
    {
        $translate = memory_registry::get("translate");
        $res = managetasks::valid_task($_POST['description_task'], $_POST['datepicker_task'], $_POST['id_group']);
        switch ($res)
        {
            case 1:
                $this->display('<div class="info" style="height:50px;"><table><tr>' .
                               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->msg('create_task_ok') . '</span></td>' .
                               '</tr></table></div>' .
                               '<script type="text/javascript">Clic("/tasks/display_tasksinprogress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>');
                break;
            
            case 0:
                $this->display('<div class="info" style="height:50px;"><table><tr>' .
                               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->msg('create_task_nok') . '</span></td>' .
                               '</tr></table></div>');
                break;
            
            case -1:
                $this->display('<div class="error" style="height:50px;"><table><tr>' .
                               '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->msg('fields_empty') . '</span></td>' .
                               '</tr></table></div>');
                break;
        }

    }
    
    
    public function delete_taskAction()
    {
        $translate = memory_registry::get("translate");
        if (managetasks::delete_task($_POST['task_id']) > 0) {
            $this->display('<div class="info" style="height:50px;"><table><tr>' .
                           '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->msg('delete_task_ok') . '</span></td>' .
                           '</tr></table></div>' .
                           '<script type="text/javascript">Clic("/tasks/display_tasksinprogress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>');
        }
        else
        {
            $this->display('<div class="error" style="height:50px;"><table><tr>' .
                           '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->msg('delete_task_nok') . '</span></td>' .
                           '</tr></table></div>');

            // Journal de log
            $member = members::getInstance();
            $db_log = new admin_logs("db");
            $db_log->log("Echec de la suppression de la tache " . $_POST['task_id'] . " par l'utilisateur " . $member->identite, admin_logs::ERR);
        }
    }
    
    
    public function edit_taskAction()
    {
        $translate = memory_registry::get("translate");
        $this->_view['translate'] = $translate;
        $this->render('admin/dev_inprogress');
    }
    
    
    public function display_tasksAction()
    {
        $translate = memory_registry::get("translate");
        
        $this->_view['translate'] = $translate;
        $this->render('admin/dev_inprogress');
    }
    
    
    public function display_tasksinprogressAction()
    {
        $this->_view['list_tasks'] = $this->_renderTasks_inprogress();
        $this->_view['translate'] = memory_registry::get("translate");
        $this->_view['id_group'] = $_POST['id_group'];
        $this->render("tasks/tasks_inprogress");
    }
    
    
    public function display_detailstaskAction()
    {
        $details = managetasks::display_detailstask($_POST['task_id']);
        
        $this->_view['translate'] = memory_registry::get("translate");
        $this->_view['details'] = $details[0];
        $this->_view['cmd_delete'] = "Clic('/tasks/delete_task', 'task_id=" . $_POST['task_id'] . "&id_group=" . $_POST['id_group'] . "', 'task_resultaction'); return false;";
        $this->_view['cmd_edit'] = "Clic('/tasks/edit_task', 'task_id=" . $_POST['task_id'] . "', 'milieu_milieu'); return false;";
        $this->render('tasks/task_details');
    }
    
    
    protected function _renderTasks_inprogress()
    {
        $list_tasks = managetasks::display_tasksinprogress($_POST['id_group']);
        $translate = memory_registry::get("translate");
        $reponse ='';
        
        if ($list_tasks->num_rows > 0) {
            while ($task = $list_tasks->fetch_assoc()) {
                $date_fin = $task['date'];
                if ($date_fin == "0000-00-00 00:00:00")      {   $date_fin = "";    }
                $reponse .= "<tr><td>" . utf8_encode($task['description']) . "</td>" .
                            "<td>" . $date_fin . "</td>" .
                            "<td>" . utf8_encode($task['identite']) . "</td>" .
                            "<td><div class='bouton'><a onclick=\"Clic('/tasks/display_detailstask', 'task_id=" . $task['idtask'] . "&id_group=" . $_POST['id_group'] . "', 'milieu_milieu'); return false;\">" .
                            "<span style='color: #dfdfdf;'>" . $translate->msg('btn_detailstask') . "</span></a></div></td>" .
                            "</tr>";
            }
        }
        else {
            $reponse .= "<tr><td>" . $translate->msg('no_result') . "</td></tr>";
        }
        
        return $reponse;
    }
}
?>
