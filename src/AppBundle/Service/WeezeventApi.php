<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 19:46
 */

namespace AppBundle\Service;

use FfjvBoBundle\Service\AppService;
use FfjvBoBundle\Entity\User;

class WeezeventApi
{
    const API_URL = 'https://api.weezevent.com/';
    const AUTH_ACCESS_URL = 'auth/access_token';
    const EVENT_URL = 'events';

    /**
     * @var AppService|null
     */
    private $appService = null;
    /**
     * @var string
     */
    private $username = '';
    /**
     * @var string
     */
    private $password = '';
    /**
     * @var string
     */
    private $apiKey = '';
    /**
     * @var string
     */
    private $accessToken = '';
    /**
     * @var \CURLFile
     */
    private $curl;

    /**
     * WeezeventApi constructor.
     * @param AppService $appService
     */
    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user){
        $this->appService->setUser($user);
        return $this;
    }
    
    /**
     * @param string $username
     * @param string $password
     * @param string $apiKey
     * @return $this
     */
    public function setAuthAccess($username = '', $password = '', $apiKey = ''){

        $this->username = $this->appService->deCrypt($username);
        $this->password = $this->appService->deCrypt($password);
        $this->apiKey = $this->appService->deCrypt($apiKey);

        return $this;
    }

    /**
     * @return $this|bool
     */
    public function initConnection(){
        $params = ['username'=>$this->username, 'password'=>$this->password, 'api_key'=>$this->apiKey];
        $options = $this->getPostOption(self::AUTH_ACCESS_URL, $params);
        $response = $this->getCurlResponse($options, true);
        if(array_key_exists('error', $response)){
            return false;
        }
        $this->accessToken = $response["accessToken"];
        return $this;
    }

    /**
     * @param bool $toArray
     * @return json|array
     */
    public function getEvents($toArray = false){
        if($this->accessToken == ''){
            $this->initConnection();
        }
        $params = ['access_token' => $this->accessToken, 'api_key' => $this->apiKey];
        $options = $this->getGetOption(self::EVENT_URL, $params);
        return $this->getCurlResponse($options, $toArray);
    }

    /**
     * @param string $apiFunction
     * @param array $params
     * @return array
     */
    private function getPostOption($apiFunction = '', array $params = []){
        return [
            CURLOPT_URL => self::API_URL.$apiFunction,
            CURLOPT_HTTPHEADER => ['content-type' => 'application/x-www-form-urlencoded', 'charset' => 'utf-8'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true
        ];
    }

    /**
     * @param string $apiFunction
     * @param array $params
     * @return array
     */
    private function getGetOption($apiFunction = '', array $params = []){
        $query = '?'.http_build_query($params);
        return array(
            CURLOPT_URL => 'https://api.weezevent.com/'.$apiFunction.$query,
            CURLOPT_HTTPHEADER => ['content-type' => 'application/x-www-form-urlencoded', 'charset' => 'utf-8'],
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => true
        );
    }

    /**
     * @param array $options
     * @param bool $toArray
     * @return json|array
     */
    private function getCurlResponse(array $options = [], $toArray = false){
        $this->curl = curl_init();
        curl_setopt_array($this->curl, $options);
        $response = curl_exec($this->curl);
        curl_close($this->curl);
        if($toArray){
            return json_decode($response, $toArray);
        }
        return $response;
    }

}