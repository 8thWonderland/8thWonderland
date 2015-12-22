<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Application\Model\ManageTasks;
use Wonderland\Application\Model\Member;

use Wonderland\Library\Admin\Log;

use Wonderland\Library\Http\Response\Response;

class TaskController extends ActionController {
    public function displayCreateTaskAction() {
        return $this->render('tasks/create_task', [
            'id_group' => $_POST['id_group']
        ]);
    }
    
    public function validTaskAction() {
        $translate = $this->application->get('translator');
        $res = ManageTasks::valid_task($_POST['description_task'], $_POST['datepicker_task'], $_POST['id_group']);
        switch ($res) {
            case 1:
                return new Response(
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('create_task_ok') . '</span></td>' .
                    '</tr></table></div>' .
                    '<script type="text/javascript">Clic("Task/displayTasksInProgress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>'
                );
            case 0:
                return new Response(
                    '<div class="info" style="height:50px;"><table><tr>' .
                    '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('create_task_nok') . '</span></td>' .
                    '</tr></table></div>'
                );
            case -1:
                return new Response(
                    '<div class="error" style="height:50px;"><table><tr>' .
                    '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
                    '<td><span style="font-size: 15px;">' . $translate->translate('fields_empty') . '</span></td>' .
                    '</tr></table></div>'
                );
        }
    }
    
    public function deleteTaskAction() {
        $translate = $this->application->get('translator');
        if (ManageTasks::delete_task($_POST['task_id']) > 0) {
            return new Response(
                '<div class="info" style="height:50px;"><table><tr>' .
                '<td><img alt="info" src="' . ICO_PATH . '64x64/Info.png" style="width:48px;"/></td>' .
                '<td><span style="font-size: 15px;">' . $translate->translate('delete_task_ok') . '</span></td>' .
                '</tr></table></div>' .
                '<script type="text/javascript">Clic("Task/displayTasksInProgress", "id_group=' . $_POST['id_group'] . '", "milieu_gauche");</script>'
            );
        }
        // Journal de log
        $member = Member::getInstance();
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $logger->log("Echec de la suppression de la tache {$_POST['task_id']} par l'utilisateur {$member->identite}", Log::ERR);

        return new Response(
            '<div class="error" style="height:50px;"><table><tr>' .
            '<td><img alt="error" src="' . ICO_PATH . '64x64/Error.png" style="width:48px;"/></td>' .
            '<td><span style="font-size: 15px;">' . $translate->translate('delete_task_nok') . '</span></td>' .
            '</tr></table></div>'
        );
    }
    
    public function editTaskAction() {
        return $this->render('admin/dev_inprogress');
    }
    
    
    public function displayTasksAction() {
        return $this->render('admin/dev_inprogress');
    }
    
    public function displayTasksInProgressAction() {
        return $this->render('tasks/tasks_inprogress', [
            'list_tasks' => $this->renderTasksInProgress(),
            'id_group' => $_POST['id_group']
        ]);
    }
    
    
    public function displayDetailsTaskAction() {
        return $this->render('tasks/task_details', [
            'details' => ManageTasks::display_detailstask($_POST['task_id'])[0],
            'cms_delete' => "Clic('Task/deleteTask', 'task_id=" . $_POST['task_id'] . "&id_group=" . $_POST['id_group'] . "', 'task_resultaction'); return false;",
            'cms_edit' => "Clic('Task/editTask', 'task_id=" . $_POST['task_id'] . "', 'milieu_milieu'); return false;"
        ]);
    }
    
    /**
     * @return string
     */
    protected function renderTasksInProgress() {
        $list_tasks = ManageTasks::display_tasksinprogress($_POST['id_group']);
        $translate = $this->application->get('translator');
        $reponse = '';
        
        if ($list_tasks->num_rows > 0) {
            while ($task = $list_tasks->fetch(\PDO::FETCH_ASSOC)) {
                $date_fin = $task['date'];
                if ($date_fin === '0000-00-00 00:00:00') {
                    $date_fin = '';
                }
                $reponse .=
                    "<tr><td>{$task['description']}</td>" .
                    "<td>$date_fin</td>" .
                    "<td>{$task['identity']}</td>" .
                    "<td><div class='bouton'><a onclick=\"Clic('Task/displayDetailsTask', 'task_id={$task['idtask']}&id_group={$_POST['id_group']}', 'milieu_milieu'); return false;\">" .
                    "<span style='color: #dfdfdf;'>{$translate->translate('btn_detailstask')}</span></a></div></td></tr>"
                ;
            }
        } else {
            $reponse .= "<tr><td>{$translate->translate('no_result')}</td></tr>";
        }
        return new Response($reponse);
    }
}