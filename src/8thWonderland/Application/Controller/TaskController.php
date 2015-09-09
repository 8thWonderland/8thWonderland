<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Memory\Registry;

use Wonderland\Application\Model\ManageTasks;
use Wonderland\Application\Model\Member;

use Wonderland\Library\Admin\Log;

class TaskController extends ActionController {

    public function display_createtaskAction()
    {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->viewParameters['id_group'] = $_POST['id_group'];
        $this->render('tasks/create_task');
    }
    
    
    public function valid_taskAction()
    {
        $translate = Registry::get("translate");
        $res = ManageTasks::valid_task($_POST['description_task'], $_POST['datepicker_task'], $_POST['id_group']);
        switch ($res)
        {
            case 1:
                $this->display('<div class="info" style="height:50px;"><table><tr>' .
                               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->translate('create_task_ok') . '</span></td>' .
                               '</tr></table></div>' .
                               '<script type="text/javascript">Clic("/tasks/display_tasksinprogress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>');
                break;
            
            case 0:
                $this->display('<div class="info" style="height:50px;"><table><tr>' .
                               '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->translate('create_task_nok') . '</span></td>' .
                               '</tr></table></div>');
                break;
            
            case -1:
                $this->display('<div class="error" style="height:50px;"><table><tr>' .
                               '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                               '<td><span style="font-size: 15px;">' . $translate->translate('fields_empty') . '</span></td>' .
                               '</tr></table></div>');
                break;
        }

    }
    
    
    public function delete_taskAction()
    {
        $translate = Registry::get("translate");
        if (ManageTasks::delete_task($_POST['task_id']) > 0) {
            $this->display('<div class="info" style="height:50px;"><table><tr>' .
                           '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->translate('delete_task_ok') . '</span></td>' .
                           '</tr></table></div>' .
                           '<script type="text/javascript">Clic("/tasks/display_tasksinprogress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>');
        }
        else
        {
            $this->display('<div class="error" style="height:50px;"><table><tr>' .
                           '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                           '<td><span style="font-size: 15px;">' . $translate->translate('delete_task_nok') . '</span></td>' .
                           '</tr></table></div>');

            // Journal de log
            $member = Member::getInstance();
            $db_log = new Log("db");
            $db_log->log("Echec de la suppression de la tache " . $_POST['task_id'] . " par l'utilisateur " . $member->identite, Log::ERR);
        }
    }
    
    
    public function edit_taskAction()
    {
        $translate = Registry::get("translate");
        $this->viewParameters['translate'] = $translate;
        $this->render('admin/dev_inprogress');
    }
    
    
    public function display_tasksAction()
    {
        $translate = Registry::get("translate");
        
        $this->viewParameters['translate'] = $translate;
        $this->render('admin/dev_inprogress');
    }
    
    
    public function display_tasksinprogressAction()
    {
        $this->viewParameters['list_tasks'] = $this->_renderTasks_inprogress();
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->viewParameters['id_group'] = $_POST['id_group'];
        $this->render("tasks/tasks_inprogress");
    }
    
    
    public function display_detailstaskAction()
    {
        $details = ManageTasks::display_detailstask($_POST['task_id']);
        
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->viewParameters['details'] = $details[0];
        $this->viewParameters['cmd_delete'] = "Clic('/tasks/delete_task', 'task_id=" . $_POST['task_id'] . "&id_group=" . $_POST['id_group'] . "', 'task_resultaction'); return false;";
        $this->viewParameters['cmd_edit'] = "Clic('/tasks/edit_task', 'task_id=" . $_POST['task_id'] . "', 'milieu_milieu'); return false;";
        $this->render('tasks/task_details');
    }
    
    
    protected function _renderTasks_inprogress()
    {
        $list_tasks = ManageTasks::display_tasksinprogress($_POST['id_group']);
        $translate = Registry::get("translate");
        $reponse ='';
        
        if ($list_tasks->num_rows > 0) {
            while ($task = $list_tasks->fetch_assoc()) {
                $date_fin = $task['date'];
                if ($date_fin == "0000-00-00 00:00:00")      {   $date_fin = "";    }
                $reponse .= "<tr><td>" . utf8_encode($task['description']) . "</td>" .
                            "<td>" . $date_fin . "</td>" .
                            "<td>" . utf8_encode($task['identite']) . "</td>" .
                            "<td><div class='bouton'><a onclick=\"Clic('/tasks/display_detailstask', 'task_id=" . $task['idtask'] . "&id_group=" . $_POST['id_group'] . "', 'milieu_milieu'); return false;\">" .
                            "<span style='color: #dfdfdf;'>" . $translate->translate('btn_detailstask') . "</span></a></div></td>" .
                            "</tr>";
            }
        }
        else {
            $reponse .= "<tr><td>" . $translate->translate('no_result') . "</td></tr>";
        }
        
        return $reponse;
    }
}