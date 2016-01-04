<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

/**
 * Controleur des administreteurs (développeurs).
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 **/
class AdminController extends ActionController
{
    public function displayConsoleAction()
    {
        // affichage du profil
        $member = $this->getUser();

        return $this->render('admin/console', [
            'identity' => $member->getIdentity(),
            'avatar' => $member->getAvatar(),
        ]);
    }

    public function quitConsoleAction()
    {
        $this->application->get('session')->delete('desktop');

        // Journal de log
        $logger = new Log('db');
        $logger->log("{$this->getUser()->getIdentity()} quitte la console d'administration.", Log::INFO);

        return $this->redirect('intranet/index');
    }

    public function displayLogsAction()
    {
        return $this->render('admin/logs', [
            'list_logs' => $this->renderLogs(),
        ]);
    }

    public function displayUsersAction()
    {
        return $this->render('admin/users', [
            'list_users' => $this->renderUsers(),
        ]);
    }

    public function displayGroupsAction()
    {
        return $this->render('admin/dev_inprogress');
    }

    public function displayServerAction()
    {
        $translate = $this->application->get('translator');
        $cronsList = $this->application->get('ovh_manager')->getCrons($this->getUser());

        return $this->render('admin/console_ovh', [
            'crons' => (isset($cronsList))
                ? $this->renderCrons($cronsList)
                :
                    '<div class="error" style="height:50px;"><table><tr>'.
                    '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:48px;"/></td>'.
                    '<td><span style="font-size: 15px;">'.$translate->translate('connexion_nok').'</span></td>'.
                    '</tr></table></div>',
        ]);
    }

    public function displayCreateCronAction()
    {
        return $this->render('admin/create_cron');
    }

    public function displayStatsCountryAction()
    {
        $db = $this->application->get('database_connection');
        $translate = $this->application->get('translator');
        $desktop = $this->application->get('session')->get('desktop');
        $templating = $this->application->get('templating');

        if (isset($desktop) && $desktop == 1) {
            $lang = $this->getUser()->getLanguage();
            $statement = $db->query("SELECT $lang, country FROM users, country WHERE region = -1 AND country = code");
            $statsOtherRegions = '<table><tr><td>'.$translate->translate('stats_region_unknown').'</td></tr>';
            while ($regionUnknown = $statement->fetch()) {
                $statsOtherRegions .= '<tr><td>- '.$regionUnknown[$lang].' ('.$regionUnknown['Pays'].')</td></tr>';
            }
            $statsOtherRegions .= '</table>';
            $templating->addParameters([
                'stats_regions_other' => $statsOtherRegions,
                'desktop' => 1,
            ]);
        }
        $regions_ok = $db->query('SELECT COUNT(*) AS count FROM users WHERE region > 0')->fetch()['count'];

        $memberManager = $this->application->get('member_manager');

        return $this->render('informations/stats_country', [
            'stats_members' => $memberManager->countMembers(),
            'stats_members_actives' => $memberManager->countActiveMembers(),
            'stats_regions_ok' => $regions_ok,
        ]);
    }

    public function addCronAction()
    {
        $translate = $this->application->get('translator');
        $err_msg = '';

        if (empty($_POST['cron_file']) || empty($_POST['cron_desc']) || empty($_POST['cron_day']) || count($_POST) < 7) {
            $err_msg = $translate->translate('fields_empty');
        } else {
            $id_cron = $this->application->get('ovh_manager')->addCron($this->getUser(), $_POST);
            switch ($id_cron) {
                case -1:
                    $err_msg = $translate->translate('file_notfound');
                    break;

                case 0:
                    $err_msg = $translate->translate('error');
                    break;

                case -2:
                    $err_msg = $translate->translate('connexion_nok');
            }
        }

        if (!empty($err_msg)) {
            return new Response(
                '<div class="error" style="height:25px;"><table><tr>'.
                '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:24px;"/></td>'.
                '<td><span style="font-size: 15px;">'.$err_msg.'</span></td>'.
                '</tr></table></div>'
            );
        }

        return new Response("<script type='text/javascript'>window.onload=Clic('Admin/displayServer', '', 'milieu_milieu');</script>");
    }

    public function deleteCronAction()
    {
        $translate = $this->application->get('translator');
        $res = $this->application->get('ovh_manager')->deleteCron($_POST['cronid'], $_POST['crondesc']);
        if (isset($res)) {
            if ($res == false) {
                return new Response(
                    '<div class="error" style="height:25px;"><table><tr>'.
                    '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:24px;"/></td>'.
                    '<td><span style="font-size: 15px;">'.$translate->translate('error').'</span></td>'.
                    '</tr></table></div>'
                );
            }

            return new Response("<script type='text/javascript'>window.onload=Clic('Admin/displayServer', '', 'milieu_milieu');</script>");
        }

        return new Response(
            '<div class="error" style="height:25px;"><table><tr>'.
            '<td><img alt="error" src="'.ICO_PATH.'64x64/Error.png" style="width:24px;"/></td>'.
            '<td><span style="font-size: 15px;">'.$translate->translate('connexion_nok').'</span></td>'.
            '</tr></table></div>'
        );
    }

    public function editCronAction()
    {
        return $this->render('admin/dev_inprogress');
    }

    /**
     * @param string $crons
     *
     * @return string
     */
    protected function renderCrons($crons)
    {
        $paginator = $this->application->get('paginator');
        $paginator->setData($crons);
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();
        $translate = $this->application->get('translator');

        $tab_crons =
            '<table id="pagination_motions" class="pagination"><tr class="entete">'.
            '<td width="20px">'.$translate->translate('cron_id').'</td>'.
            '<td>'.$translate->translate('cron_file').'</td>'.
            '<td>'.$translate->translate('cron_description').'</td>'.
            '<td width="40px">'.$translate->translate('cron_active').'</td>'.
            '<td></td></tr>'
        ;

        foreach ($datas as $key => $row) {
            $tab_crons .= "<tr style='height:25px'>";
            foreach ($row as $key => $value) {
                if ($key != 'email') {
                    $tab_crons .= "<td>{$this->filterCrons($key, $value)}</td>";
                }
            }
            $array = get_object_vars($row);
            $tab_crons .=
                "<td><a onclick=\"Clic('Admin/editCron', 'idcron=".$array['id']."', 'server_infoaction'); return false;\">".
                "<img width='24' src='".ICO_PATH."32x32/edit.png'/></a>".
                "<a onclick=\"Clic('Admin/deleteCron', 'cronid=".$array['id'].'&crondesc='.$array['desc']."', 'server_infoaction'); return false;\">".
                "<img width='24' src='".ICO_PATH."32x32/delete.png'/></a></td></tr>"
            ;
        }
        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);

        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_crons .= '<tr class="pied"><td colspan="2" align="left">'.$nFirstItem.'-'.$nLastItem.$translate->translate('item_of').$paginator->countItems().'</td>';

        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">'.$translate->translate('page_previous').'</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Admin/displayServer\', \'&page='.($CurPage - 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_previous').'</a>';
        }
        $tab_crons .= '<td colspan="4" style="padding-right:15px;" align="right">'.$previous.' | ';
        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;
        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }

        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_crons .=
                ($page != $CurPage)
                ? '<a onclick="Clic(\'Admin/displayServer\', \'&page='.$page.'\', \'milieu_milieu\'); return false;">'.$page.'</a> | '
                : $tab_crons .= '<b>'.$page.'</b> | '
            ;
        }
        $next = '<span class="disabled">'.$translate->translate('page_next').'</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Admin/displayServer\', \'&page='.($CurPage + 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_next').'</a>';
        }

        return
            $tab_crons.$next.'</td></tr></table>'.
            "<div class='bouton' style='width:120px;'><a onclick=\"Clic('Admin/displayCreateCron', '', 'milieu_milieu'); return false;\">".
            "<span style='color: #dfdfdf;'>".$translate->translate('btn_addcron').'</span></a></div>'
        ;
    }

    /**
     * @return string
     */
    protected function renderLogs()
    {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('logger'));
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }
        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();

        $translate = $this->application->get('translator');

        $tab_logs =
            '<table id="pagination_motions" class="pagination"><tr class="entete">'.
            '<td width="20px">'.$translate->translate('logs_level').'</td>'.
            '<td width="150px">'.$translate->translate('logs_date').'</td>'.
            '<td>'.$translate->translate('logs_description').'</td></tr>'
        ;

        foreach ($datas as $key => $row) {
            $tab_logs .= "<tr style='height:25px'>";
            foreach ($row as $key => $value) {
                if ($key !== 'Motion_id') {
                    $tab_logs .= "<td>{$this->filterLogs($key, $value)}</td>";
                }
            }
            $tab_logs .= '</tr>';
        }

        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);

        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_logs .= '<tr class="pied"><td colspan="2" align="left">'.$nFirstItem.'-'.$nLastItem.$translate->translate('item_of').$paginator->countItems().'</td>';

        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">'.$translate->translate('page_previous').'</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Admin/displayLogs\', \'&page='.($CurPage - 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_previous').'</a>';
        }
        $tab_logs .= '<td style="padding-right:15px;" align="right">'.$previous.' | ';

        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;

        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }

        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_logs .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'Admin/displayLogs\', \'&page='.$page.'\', \'milieu_milieu\'); return false;">'.$page.'</a> | '
                : '<b>'.$page.'</b> | '
            ;
        }
        $next = '<span class="disabled">'.$translate->translate('page_next').'</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Admin/displayLogs\', \'&page='.($CurPage + 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_next').'</a>';
        }

        return $tab_logs.$next.'</td></tr></table>';
    }

    /**
     * @return string
     */
    protected function renderUsers()
    {
        $paginator = $this->application->get('paginator');
        $paginator->setData($this->application->get('member_manager')->getMembers());
        $paginator->setCurrentPage(1);
        if (!empty($_POST['page'])) {
            $paginator->setCurrentPage($_POST['page']);
        }

        $datas = $paginator->getCurrentItems();
        $CurPage = $paginator->getCurrentPage();
        $MaxPage = $paginator->getNumPage();

        $translate = $this->application->get('translator');

        $tab_users =
            '<table id="pagination_users" class="pagination"><tr class="entete">'.
            '<td width="50px">'.$translate->translate('avatar').'</td>'.
            '<td width="200px">'.$translate->translate('identity').'</td>'.
            '<td width="50px">'.$translate->translate('gender').'</td>'.
            '<td width="200px">'.$translate->translate('mail').'</td>'.
            '<td width="50px">'.$translate->translate('lang').'</td>'.
            '<td width="150px">'.$translate->translate('country').'</td>'.
            '<td width="150px">'.$translate->translate('region').'</td>'.
            '<td width="150px">'.$translate->translate('last_connexion').'</td>'.
            '<td width="150px">'.$translate->translate('subscription').'</td></tr>'
        ;

        foreach ($datas as $key => $row) {
            $tab_users .= "<tr style='height:25px'>";
            foreach ($row as $key => $value) {
                if ($key != 'iduser') {
                    $tab_users .= '<td>'.$this->filterUsers($key, $value).'</td>';
                }
            }
            $tab_users .= '</tr>';
        }

        // numéros des items
        $itemsPage = $paginator->getItemsPerPage();
        $nFirstItem = (($CurPage - 1) * $itemsPage) + 1;
        $nLastItem = ($CurPage * $itemsPage);

        $items = $paginator->countItems();
        if ($nLastItem > $items) {
            $nLastItem = $items;
        }
        $tab_users .= '<tr class="pied"><td colspan="6" align="left">'.$nFirstItem.'-'.$nLastItem.$translate->translate('item_of').$paginator->countItems().'</td>';

        // boutons precedent, suivant et numéros des pages
        $previous = '<span class="disabled">'.$translate->translate('page_previous').'</span>';
        if ($CurPage > 1) {
            $previous = '<a onclick="Clic(\'Admin/displayUsers\', \'&page='.($CurPage - 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_previous').'</a>';
        }
        $tab_users .= '<td style="padding-right:15px;" align="right" colspan="3">'.$previous.' | ';

        $pageRange = $paginator->getPageRange();
        $start = $CurPage - $pageRange;
        $end = $CurPage + $pageRange;

        if ($start < 1) {
            $start = 1;
        }
        if ($end > $MaxPage) {
            $end = $MaxPage;
        }

        for ($page = $start; $page < $end + 1; ++$page) {
            $tab_users .=
                ($page !== $CurPage)
                ? '<a onclick="Clic(\'Admin/displayUsers\', \'&page='.$page.'\', \'milieu_milieu\'); return false;">'.$page.'</a> | '
                : $tab_users .= '<b>'.$page.'</b> | '
            ;
        }
        $next = '<span class="disabled">'.$translate->translate('page_next').'</span>';

        // Bouton suivant
        if ($CurPage < $MaxPage) {
            $next = '<a onclick="Clic(\'Admin/displayUsers\', \'&page='.($CurPage + 1).'\', \'milieu_milieu\'); return false;">'.$translate->translate('page_next').'</a>';
        }

        return $tab_users.$next.'</td></tr></table>';
    }

    // Affichage du filtre de données si il existe
    // ===========================================
    protected function filterLogs($key, $value)
    {
        if (strtolower($key) === 'label_key') {
            $value = $this->application->get('translator')->translate($value);
        }

        return $value;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function filterUsers($key, $value)
    {
        switch (strtolower($key)) {
            case 'avatar':
                return '<img width="50" alt="Avatar" src="'.$value.'">';

            case 'identite':
                return utf8_encode($value);

            case 'sexe':
                return $value == 1 ? 'M' : 'F';

            case 'pays':
                $lang = $this->getUser()->getLanguage();
                $res = $this->application->get('database_connection')->select("SELECT $lang FROM country WHERE code = '$value' LIMIT 1");

                return
                    (count($res) > 0)
                    ? $res[0][$lang]
                    : $this->application->get('translator')->translate('unknown')
                ;

            case 'region':
                $res = $this->application->get('db')->select("SELECT Name FROM regions WHERE Region_id = $value LIMIT 1");

                return
                    (count($res) > 0 && $value > 0)
                    ? utf8_encode($res[0]['Name'])
                    : $this->application->get('translator')->translate('unknown')
                ;
            default:
                return $value;
        }
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function filterCrons($key, $value)
    {
        switch (strtolower($key)) {
            case 'desc':
                return utf8_encode($value);
            case 'enabled':
                return
                    ($value === 1)
                    ? '<img width="16" alt="active" src="'.ICO_PATH.'32x32/valid.png">'
                    : '<img width="16" alt="inactive" src="'.ICO_PATH.'32x32/echec.png">'
                ;
            default:
                return $value;
        }
    }
}
