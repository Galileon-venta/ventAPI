<?php

namespace ventAPI;
use Exception;
use GuzzleHttp;

require 'vendor/autoload.php';

class ventAPI{
    private $http;
    /**
     * @var int
     */

    /**
     * ventAPI constructor.
     */
    public function __construct()
    {
        $this->http = new GuzzleHttp\Client();
    }

    public function ventAPI_get_game_groups(){
        $url = "http://ventaGaming.de:9090/api/secure/groups/gamegroups/webpanel";
        return json_decode($this->get_secure($url),true);
    }

    public function ventAPI_get_random_feedback($count){
        $url = "http://ventaGaming.de:9090/api/secure/web/getFeedback/random/".$count."";
        return json_decode($this->get_secure($url));
    }

    public function ventAPI_login($username,$password){
        global $http;
        $credentials['username'] = $username;
        $credentials['password'] = $password;
        try {
            $response = $this->http->request('POST', 'http://ventagaming.de:9090/api/auth/token',
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
        catch (Exception $e){
            error_log("Can't Authenticate; API is offline");
        }
    }

    private function get_secure($url){
        if(!isset($_SESSION['ventAPItoken'])){
            error_log('Not logged in for Secure Action');
            return [];
        }
        return $this->http->request('GET',$url,[
            'headers' => [
                'AUTHORIZATION' => 'Bearer '.$_SESSION['ventAPItoken'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ])->getBody();
    }
}
