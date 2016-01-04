<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class IndexController extends ActionController
{
    /**
     * @return \Wonderland\Library\Http\Response\Response
     */
    public function indexAction()
    {
        // controle si l'utilisateur est déjà connecté
        if ($this->getUser() !== null) {
            return $this->redirect('intranet/index');
        }

        return $this->render('accueil', [
            'appli_status' => 1,
            'msg' => '',
            'countries' => $this->application->get('country_manager')->getCountries(),
        ]);
    }

    public function presentationAction()
    {
        return $this->render('informations/presentation');
    }

    public function subscribeAction()
    {
        $translate = $this->application->get('translator');
        $langs = $translate->getList();
        $sel_lang = '';
        $nbLangs = count($langs);
        for ($i = 0; $i < $nbLangs; ++$i) {
            $sel_lang .= "<option value='{$langs[$i]}'>{$translate->translate($langs[$i])}</option>";
        }

        return $this->render('members/subscribe', [
            'langs' => $sel_lang,
        ]);
    }

    public function partnersAction()
    {
        return $this->render('informations/partners');
    }

    public function newsAction()
    {
        $facebookManager = $this->application->get('facebook_manager');

        return $this->render('informations/public_news', [
            'facebook_feed' => $facebookManager->getPageFeed(3),
            'facebook_picture' => $facebookManager->getPagePicture(),
            'facebook_page' => $facebookManager->getPageInformations(),
        ]);
    }

    public function contactAction()
    {
        return $this->render('communications/contactus');
    }
}
