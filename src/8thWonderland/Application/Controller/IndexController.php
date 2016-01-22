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
}
