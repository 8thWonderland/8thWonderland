<?php

/**
 * Gestion des connexions au site web
 *
 * @author: BrennanWaco - waco.brennan@gmail.com
 *
 **/


class index extends controllers_action {   
    public function indexAction()
    {
        // controle si l'utilisateur est déjà connecté
        if (auth::hasIdentity())    {   $this->redirect("intranet/index");      }

        $this->_view['appli_status'] = 1;
        $this->_view['translate'] = memory_registry::get("translate");
	$this->_view['msg'] = '';
        $this->default_file = APPLICATION_PATH . "views/informations/presentation.view";

        $this->render("accueil");
        
    }


    function presentationAction() {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('informations/presentation');
    }


    function subscribeAction() {
        $translate = memory_registry::get("translate");
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
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('informations/partners');
    }


    function newsAction() {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->render('informations/public_news');
    }


    function contactAction() {
        $this->_view['translate'] = memory_registry::get("translate");
        $this->contact_status = "";
        $this->render('communications/contactus');
    }
}