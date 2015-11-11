<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class CountryController extends ActionController {
    public function getCountriesAction() {
        header('Content-Type: application/json');
        
        echo json_encode($this->application->get('country_manager')->getCountries());
    }
}