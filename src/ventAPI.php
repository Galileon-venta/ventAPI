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

    public function ventAPI_preflight_Steam_Connection($dbid, $request){
        $url = "http://ventaGaming.de:9090/api/secure/tsClients/steampreflight/".$dbid."/".$request;
        return $this->get_secure($url);
    }

    public function ventAPI_confirm_Steam_Account($dbid, $request, $steamid){
        $url = "http://ventaGaming.de:9090/api/secure/tsClients/steamconfirmation/".$dbid."/".$request;
        return $this->post_secure($url, $steamid);
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


    private function post_secure($url, $body){
        if(!isset($_SESSION['ventAPItoken'])){
            error_log('Not logged in for Secure Action');
            return [];
        }
        return $this->http->request('POST',$url,[
            'body' => json_encode($body),
            'headers' => [
                'AUTHORIZATION' => 'Bearer '.$_SESSION['ventAPItoken'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ])->getBody();
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
