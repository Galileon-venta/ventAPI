<?php

namespace ventAPI;
use GuzzleHttp;

require 'vendor/autoload.php';

class ventAPI{
    private $http;

    /**
     * ventAPI constructor.
     */
    public function __construct()
    {
        $this->http = new GuzzleHttp\Client();
    }

    public function ventAPI_get_game_groups(){
        $url = "http://ventaGaming.de:9090/api/secure/groups/gamegroups/webpanel";
        return json_decode($this->file_get_contents_secure($url), true);
    }

    public function ventAPI_get_random_feedback(){
        $url = "http://ventaGaming.de:9090/api/secure/web/getFeedback/random";
        return json_decode($this->file_get_contents_secure($url));
    }

    public function ventAPI_login($username,$password){
        global $http;
        $credentials['username'] = $username;
        $credentials['password'] = $password;
        $response = $this->http->request('POST','http://ventagaming.de:9090/api/auth/token',
            [
                'body' => json_encode($credentials)
                ,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*',
                ]
            ]);
        $_SESSION['ventAPItoken'] = $response->getBody();
    }

    private function file_get_contents_secure($url){
        return $this->http->request('GET',$url,[
            'headers' => [
                'AUTHORIZATION' => 'Bearer '.$_SESSION['ventAPItoken'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ])->getBody();
    }
}
