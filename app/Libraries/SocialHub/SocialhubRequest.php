<?php namespace App\Libraries\SocialHub;

use anlutro\cURL\cURL;
use anlutro\cURL\Request;
use anlutro\cURL\Response;
use anlutro\cURL\cURLException;

class SocialhubRequest {
    protected static $DOMAIN = 'http://socialhub.innity-asia.com/';
    protected static $METHOD_GET = 'get';
    protected $apiName = '';
    protected $curl = null;
    
    public function __construct() {
        $this->curl = new cURL();
    }
    
    protected function getJsonRequest() {
        return $this->curl->newJsonRequest(
            self::$METHOD_GET,
            self::$DOMAIN.$this->apiName.'&limit=50&since='.(time()-3600)
        )->setHeader('Accept', 'application/json')
        ->setHeader('Content-Type', 'application/json');
    }
    
    private function getNextJsonRequest($URL) {
        return $this->curl->newJsonRequest(
            self::$METHOD_GET,
            $URL
        )->setHeader('Accept', 'application/json')
        ->setHeader('Content-Type', 'application/json');
    }
    
    protected function send(Request $request) {
        try {
            $response = $request->send();
        } catch (cURLException $e) {
            $response = null;
        }
        
        return $this->processResponse($response);
    }
    
    protected function processResponse(Response $response = null)
    {
        $simpleResponse = [
            'body' => '',
            'statusText' => '',
            'statusCode' => -1
        ];

        if (!is_null($response)) {
            $simpleResponse['body'] = $response->body;
            $simpleResponse['statusText'] = $response->statusText;
            $simpleResponse['statusCode'] = $response->statusCode;
        }
        
        return $simpleResponse;
    }
    
    public function get() {
        
        $request = $this->getJsonRequest();
        
        return $this->send($request);
    }
    
    public function getNextPage($URL) {
        
        $request = $this->getNextJsonRequest($URL);
        
        return $this->send($request);
    }
}

