<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;
use Wonderland\Application\Model\Mailer;
use Wonderland\Library\Admin\Log;
use Wonderland\Library\Http\Response\Response;
use Wonderland\Library\Http\Response\JsonResponse;

class IntranetController extends ActionController
{
    /**
     * @return \Wonderland\Library\Http\Response\AbstractResponse
     */
    public function indexAction()
    {
        if (($member = $this->getUser()) === null) {
            return $this->redirect('public/index');
        }

        return $this->render('intranet', [
            'identity' => $member->getIdentity(),
            'avatar' => $member->getAvatar(),
            'admin' => $this->application->get('group_manager')->isMemberInGroup($member, 1),
            'motions' => $this->application->get('motion_manager')->getActiveMotions($member),
            'groups' => $member->getGroups(),
            'nb_unread_messages' => $this->application->get('message_manager')->countUnreadMessages($member->getId()),
        ]);
    }
    // TO REMOVE
    public function groupAction()
    {
        if (($member = $this->getUser()) === null) {
            return $this->redirect('public/index');
        }
        $desktop = $this->application->get('session')->get('desktop');
        $this->application->get('templating')->addParameters([
            'Contact_Group' => $this->get('member_manager')->isContact($member, $desktop),
            'haut_milieu' => ($desktop === 1)
                ? VIEWS_PATH.'admin/menu_admin.view'
                : VIEWS_PATH.'groups/menu_groups.view',
            'milieu_droite' => '<table>'.
                "<tr><td id='md_section1'><script type='text/javascript'>window.onload=Clic('Admin/displayStatsCountry', '', 'md_section1');</script></td></tr>".
                "<tr><td id='md_section2'><script type='text/javascript'>window.onload=Clic('Group/displayMembers', '', 'md_section2');</script></td></tr>".
                '</table>',
            'milieu_milieu' => '',
            'milieu_gauche' => "<script type='text/javascript'>window.onload=Clic('Member/displayContactsGroups', '', 'milieu_gauche');</script>",
            'user_id' => $member->getId(),
            'group_id' => $desktop,
        ]);
    }

    public function openChatroomAction()
    {
        if (($member = $this->getUser()) !== null) {
            $this->application->get('templating')->addParameters([
                'username' => $member->getIdentity(),
                'avatar' => $member->getAvatar(),
            ]);
        } else {
            $this->application->get('templating')->addParameters([
                'username' => 'guest',
                'avatar' => ICO_PATH.'user-48.png',
            ]);
        }

        return $this->render('communications/chatroom', [
            'chatrooms' => $this->application->get('chatrooms'),
        ]);
    }

    public function zoneGeoAction()
    {
        if (($member = $this->getUser()) === null) {
            return $this->redirect('Index/index');
        }

        if (!empty($_POST['country']) && isset($_POST['region']) && $_POST['region'] !== 0) {
            $db = $this->application->get('database_connection');
            $db->prepareStatement(
                'UPDATE users SET country = :country, region = :region WHERE id = :id', [
                'country' => $_POST['country'],
                'region' => $_POST['region'],
                'id' => $member->getId(),
            ]);

            if ($_POST['region'] !== -1) {
                $logger = $this->application->get('logger');
                $logger->setWriter('db');
                // Ajout de l'utilisateur dans le groupe correspondant
                // ===================================================
                $echec_createGroup = false;
                $groupId = 0;
                $groupName = $db->prepareStatement(
                    'SELECT Name FROM regions WHERE Region_id = :region_id', ['region_id' => $_POST['region']])->fetch()['Name'];
                if ($db->prepareStatement('SELECT COUNT(*) AS count FROM groups WHERE name = :name', ['name' => $groupName])->fetch()['count'] === 0) {
                    $statement = $db->prepareStatement(
                        'INSERT INTO groups (type_id, description, name, contact_id) VALUES (:type_id, :description, :name, :contact_id)', [
                            'type_id' => 1,
                            'description' => 'Groupe regional',
                            'name' => $groupName,
                            'contact_id' => 1,
                        ]);
                    if ($statement->rowCount() === 0) {
                        $echec_createGroup = true;
                    } else {
                        // Journal de log
                        $logger->log("Création du groupe régional $groupName par l'utilisateur ".$member->getIdentity(), Log::ERR);
                        $groupId = $db->lastInsertId();
                    }
                } else {
                    $groupId = $db->query("SELECT id FROM groups WHERE name = '$groupName'")->fetch()['id'];
                }

                if (!$echec_createGroup) {
                    $statement = $db->prepareStatement(
                        'INSERT INTO citizen_groups (citizen_id, group_id) VALUES (:citizen_id, :group_id)', ['citizen_id' => $member->getId(), 'group_id' => $groupId]);
                    if ($statement->rowCount() === 1) {
                        // Journal de log
                        $logger->log("Ajout de l'utilisateur {$member->getIdentity()} dans le groupe {$groupName}", Log::ERR);
                    }
                }
            } else {
                // Si la region choisie est 'other' alors Brennan Waco reçoit un mail
                // ==================================================================
                $mail = new Mailer();
                $mail->addRecipient('kern046@gmail.com', '');
                $mail->addFrom('developpeurs@8thwonderland.com', '');
                $mail->addSubject('regions inconnues', '');
                $mail->html =
                    "<table><tr><td>ID User : {$member->getIdentity()} :<br/>====================</td></tr>".
                    "<tr><td>{$_POST['country']}<br/></td></tr>".
                    '<tr><td>Message :<br/>====================</td></tr></table>'
                ;
                $mail->send();
            }

            return $this->redirect('Intranet/index');
        }

        return new JsonResponse([
            'status' => 2,
            'reponse' => $this->application->get('translator')->translate('fields_empty'),
        ]);
    }

    public function listRegionsAction()
    {
        $res = '<option></option>';
        if (!empty($_POST['country'])) {
            $statement = $this->application->get('database_connection')->prepareStatement(
                'SELECT Region_id, Name FROM regions WHERE Country = :country ORDER BY Name ASC', ['country' => $_POST['country']]);
            $regions = '';
            while ($region = $statement->fetch()) {
                $regions .= "<option value='{$region['Region_id']}'>".htmlentities($region['Name']).'</option>';
            }
            if (empty($regions)) {
                $regions .= "<option value='-1'>Other</option>";
            }
        }

        return new Response($res.$regions);
    }

    public function infosAction()
    {
        if ($this->getUser() === null) {
            return $this->redirect('Index/index');
        }
        $facebookManager = $this->application->get('facebook_manager');

        return $this->render('informations/public_news', [
            'facebook_feed' => $facebookManager->getPageFeed(3),
            'facebook_picture' => $facebookManager->getPagePicture(),
            'facebook_page' => $facebookManager->getPageInformations(),
        ]);
    }

    public function shareAction()
    {
        if ($this->getUser() === null) {
            return $this->redirect('Index/index');
        }

        return $this->render('admin/dev_inprogress');
    }

    public function communicateAction()
    {
        if ($this->getUser() === null) {
            return $this->redirect('Index/index');
        }
        $facebookManager = $this->application->get('facebook_manager');

        return $this->render('informations/public_news', [
            'facebook_feed' => $facebookManager->getPageFeed(3),
            'facebook_picture' => $facebookManager->getPagePicture(),
            'facebook_page' => $facebookManager->getPageInformations(),
        ]);
    }

    public function financeAction()
    {
        if ($this->getUser() === null) {
            return $this->redirect('Index/index');
        }

        return $this->render('admin/dev_inprogress');
    }

    public function consoleAction()
    {
        if (($member = $this->getUser()) === null) {
            return $this->redirect('Index/index');
        }
        $memberManager = $this->application->get('member_manager');
        if (!$memberManager->isMemberInGroup($member->getId(), 1)) {
            return $this->redirect('Intranet/index');
        }
        $logger = $this->application->get('logger');
        $logger->setWriter('db');
        $logger->log("{$member->getIdentity()} entre dans la console d'administration.", Log::INFO);

        return $this->redirect('Admin/displayConsole');
    }
}
