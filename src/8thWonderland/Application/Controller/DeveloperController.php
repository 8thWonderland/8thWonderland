<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class DeveloperController extends ActionController {
    public function installationGuideAction() {
        return $this->render('developer/installation_guide');
    }
}