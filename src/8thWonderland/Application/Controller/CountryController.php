<?php

namespace Wonderland\Application\Controller;

use Wonderland\Library\Controller\ActionController;

use Wonderland\Library\Exception\BadRequestException;

use Wonderland\Library\Http\Response\JsonResponse;

class CountryController extends ActionController {
    public function allAction() {
        return new JsonResponse($this->application->get('country_manager')->getCountries());
    }
    
    public function regionsAction() {
        if(!isset($_GET['country_id'])) {
            throw new BadRequestException(
                $this->application->get('translator')->translate('regions.missing_country_argument')
            );
        }
        return new JsonResponse($this->application->get('region_manager')->getRegionsByCountry($_GET['country_id']));
    }
}