<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class IndexController extends ActionController {   
    public function indexAction() {
        // controle si l'utilisateur est déjà connecté
        if ($this->getUser() !== null) {
            $this->redirect('Intranet/index');
        }
        $this->render('accueil', [
            'appli_status' => 1,
            'msg' => ''
        ]);
    }

    function presentationAction() {
        $this->render('informations/presentation');
    }

    function subscribeAction() {
        $translate = $this->application->get('translator');
        $langs = $translate->getList();
        $sel_lang = '';
        $nbLangs = count($langs);
        for ($i = 0; $i < $nbLangs; ++$i) {
            $sel_lang .= "<option value='{$langs[$i]}'>{$translate->translate($langs[$i])}</option>";
        }
        
        $this->render('members/subscribe', [
            'langs' => $sel_lang
        ]);
    }

    function partnersAction() {
        $this->render('informations/partners');
    }

    function newsAction() {
        $this->render('informations/public_news');
    }

    function contactAction() {
        $this->contact_status = '';
        $this->render('communications/contactus');
    }
}