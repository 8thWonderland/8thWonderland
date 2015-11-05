<?php

namespace Wonderland\Application\Manager;

use GuzzleHttp\Client;

class FacebookManager {
    /** @var string **/
    protected $pageId;
    /** @var string **/
    protected $apiUrl;
    /**
     * @param string $pageId
     */
    public function __construct($pageId) {
        
    }
    
    public function getPageAccessToken() {
        
    }
    
    public function getPageFeed($limit = 10) {
        $accessToken = $this->facebookManager->getAccessToken();
        
        $client = new Client();
        $response = $client->request('GET', "{$this->apiUrl}/{$this->pageId}/feed?access_token=$accessToken&limit=$limit");
    }
}