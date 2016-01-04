<?php

namespace Wonderland\Application\Manager;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FacebookManager
{
    /** @var string **/
    protected $apiUrl;
    /** @var string **/
    protected $appId;
    /** @var string **/
    protected $appSecret;
    /** @var string **/
    protected $pageId;
    /** @var \GuzzleHttp\Client **/
    protected $client;

    /**
     * @param string $apiUrl
     * @param string $appId
     * @param string $appSecret
     * @param string $pageId
     */
    public function __construct($apiUrl, $appId, $appSecret, $pageId)
    {
        $this->apiUrl = $apiUrl;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->pageId = $pageId;
        $this->client = new Client();
    }

    /**
     * @return string
     */
    public function getPageAccessToken()
    {
        try {
            $response = $this->client->request('GET',
                "{$this->apiUrl}/oauth/access_token?
                client_id={$this->appId}&client_secret={$this->appSecret}&
                grant_type=client_credentials"
            );
            var_dump($response->getBody());
            die;
        } catch (ClientException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getResponse()->getStatusCode());
            var_dump($ex->getResponse()->getBody()->getContents());
            die;
        }
    }

    /**
     * @param type $limit
     *
     * @return array
     */
    public function getPageFeed($limit = 10)
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}/{$this->pageId}/posts?access_token={$this->appId}|{$this->appSecret}&limit=$limit");

            $body = $response->getBody();
            if ($body->eof()) {
                $body->close();
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getResponse()->getStatusCode());
            var_dump($ex->getResponse()->getBody()->getContents());
            die;
        }
    }

    public function getPagePicture($width = '64', $height = '64')
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}/{$this->pageId}/picture?redirect=false&width=$width&height=$height");

            $body = $response->getBody();
            if ($body->eof()) {
                $body->close();
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getResponse()->getStatusCode());
            var_dump($ex->getResponse()->getBody()->getContents());
            die;
        }
    }

    public function getPageInformations()
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}/{$this->pageId}?access_token={$this->appId}|{$this->appSecret}&fields=name,description,cover");

            $body = $response->getBody();
            if ($body->eof()) {
                $body->close();
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getResponse()->getStatusCode());
            var_dump($ex->getResponse()->getBody()->getContents());
            die;
        }
    }
}
