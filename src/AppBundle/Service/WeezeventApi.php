<?php
/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 27/05/16
 * Time: 19:46
 */

namespace AppBundle\Service;

class WeezeventApi
{
    const API_URL = 'https://api.weezevent.com/';
    const AUTH_ACCESS_URL = 'auth/access_token';
    const EVENT_URL = 'events';
    /**
     * @var string
     */
    private $username = 'l.negre@weezevent.com';
    /**
     * @var string
     */
    private $password = '*Avier016';
    /**
     * @var string
     */
    private $apiKey = 'e938f10d417115b6c9bc05f578868c4c';
    /**
     * @var string
     */
    private $accessToken = '';
    /**
     * @var \CURLFile
     */
    private $curl;

    /**
     * @param string $username
     * @param string $password
     * @param string $apiKey
     * @return $this
     */
    public function setAuthAccess($username = '', $password = '', $apiKey = ''){

        $this->username = $username;
        $this->password = $password;
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function initConnection(){
        $params = ['username'=>$this->username, 'password'=>$this->password, 'api_key'=>$this->apiKey];
        $options = $this->getPostOption(self::AUTH_ACCESS_URL, $params);
        $response = $this->getCurlResponse($options, true);
        if(array_key_exists('error', $response)){
            throw new Exception($response['error']['message']);
        }
        $this->accessToken = $response["accessToken"];
        return $this;
    }

    /**
     * @param bool $toArray
     * @return json|array
     * @throws Exception
     */
    public function getListEvent($toArray = false){
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