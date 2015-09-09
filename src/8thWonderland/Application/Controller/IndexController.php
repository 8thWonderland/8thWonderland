<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Auth;

use Wonderland\Library\Memory\Registry;

class IndexController extends ActionController {   
    public function indexAction()
    {
        global $application;
        // controle si l'utilisateur est déjà connecté
        if (Auth::hasIdentity()) {
            $this->redirect("Intranet/index");
        }

        $this->viewParameters['appli_status'] = 1;
        $this->viewParameters['translate'] = Registry::get("translate");
	$this->viewParameters['msg'] = '';
        $this->default_file = "{$application->getRootPath()}Application/views/informations/presentation.view";

        $this->render("accueil");
        
    }


    function presentationAction() {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('informations/presentation');
    }


    function subscribeAction() {
        $translate = Registry::get("translate");
        $langs = $translate->getList();
        $sel_lang = '';
        for ($i=0; $i<count($langs); $i++) {
            $sel_lang .= "<option value='" . $langs[$i] . "'>" . $translate->translate($langs[$i]) . "</option>";
        }
        
        $this->viewParameters['translate'] = $translate;
        $this->viewParameters['langs'] = $sel_lang;
        $this->render('members/subscribe');
    }


    function partnersAction() {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('informations/partners');
    }


    function newsAction() {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->render('informations/public_news');
    }


    function contactAction() {
        $this->viewParameters['translate'] = Registry::get("translate");
        $this->contact_status = "";
        $this->render('communications/contactus');
    }
}