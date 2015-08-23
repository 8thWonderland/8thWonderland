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

        $this->_view['appli_status'] = 1;
        $this->_view['translate'] = Registry::get("translate");
	$this->_view['msg'] = '';
        $this->default_file = "{$application->getRootPath()}Application/views/informations/presentation.view";

        $this->render("accueil");
        
    }


    function presentationAction() {
        $this->_view['translate'] = Registry::get("translate");
        $this->render('informations/presentation');
    }


    function subscribeAction() {
        $translate = Registry::get("translate");
        $langs = $translate->getList();
        $sel_lang = '';
        for ($i=0; $i<count($langs); $i++) {
            $sel_lang .= "<option value='" . $langs[$i] . "'>" . $translate->msg($langs[$i]) . "</option>";
        }
        
        $this->_view['translate'] = $translate;
        $this->_view['langs'] = $sel_lang;
        $this->render('members/subscribe');
    }


    function partnersAction() {
        $this->_view['translate'] = Registry::get("translate");
        $this->render('informations/partners');
    }


    function newsAction() {
        $this->_view['translate'] = Registry::get("translate");
        $this->render('informations/public_news');
    }


    function contactAction() {
        $this->_view['translate'] = Registry::get("translate");
        $this->contact_status = "";
        $this->render('communications/contactus');
    }
}