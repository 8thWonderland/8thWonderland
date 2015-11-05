<?php

namespace Wonderland\Application\Manager;

use GuzzleHttp\Client;

class NewsManager {
    /** @var \Wonderland\Application\Manager\FacebookManager **/
    protected $facebookManager;
    
    /**
     * @param \Wonderland\Application\Manager\FacebookManager $facebookManager
     */
    public function __construct(FacebookManager $facebookManager) {
        $this->facebookManager = $facebookManager;
    }
    
    public function getFacebookFeed() {
        return $this->facebookManager->getPageFeed();
    }
}