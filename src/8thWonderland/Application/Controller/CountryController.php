<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

class CountryController extends ActionController {
    public function allAction() {
        header('Content-Type: application/json');
        
        echo json_encode($this->application->get('country_manager')->getCountries());
    }
    
    public function regionsAction() {
        if(!isset($_GET['country_id'])) {
            throw new \InvalidArgumentException(
                $this->application->get('translator')->translate('regions.missing_country_argument')
            );
        }
        header('Content-Type: application/json');
        
        echo json_encode($this->application->get('region_manager')->getRegionsByCountry($_GET['country_id']));
    }
}