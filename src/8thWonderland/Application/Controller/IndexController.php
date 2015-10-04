<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class IndexController extends ActionController {   
    public function indexAction() {
        // controle si l'utilisateur est déjà connecté
        if ($this->application->get('session')->get('__id__') !== null) {
            $this->redirect('Intranet/index');
        }

        $this->viewParameters['appli_status'] = 1;
        $this->viewParameters['translate'] = $this->application->get('translator');
	$this->viewParameters['msg'] = '';
        $this->default_file = "{$this->application->getRootPath()}Application/views/informations/presentation.view";

        $this->render('accueil');
    }

    function presentationAction() {
        $this->viewParameters['translate'] = $this->application->get('translator');
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
        
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['langs'] = $sel_lang;
        $this->render('members/subscribe');
    }

    function partnersAction() {
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('informations/partners');
    }


    function newsAction() {
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->render('informations/public_news');
    }


    function contactAction() {
        $this->viewParameters['translate'] = $this->application->get('translator');
        $this->contact_status = '';
        $this->render('communications/contactus');
    }
}