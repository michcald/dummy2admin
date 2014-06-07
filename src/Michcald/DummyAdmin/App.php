<?php

namespace Michcald\DummyAdmin;

class App
{
    private $endpoint;
    
    private $name;
    
    private $password;
    
    private $restClient;
    
    private $auth;
    
    private $identityMap = array();
    
    public function __construct()
    {
        $this->restClient = new \Michcald\RestClient\Client();
        $this->auth = new \Michcald\RestClient\Auth\Basic();
    }
    
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        
        return $this;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
    
    public function call($method, $uri, array $data = array())
    {
        $this->auth->setUsername($this->name)
            ->setPassword($this->password);
        
        $this->restClient->setAuth($this->auth);
        
        $url = $this->endpoint . '/' . $uri;

        switch ($method) {
            case 'get':
                $key = md5($uri);
                if (array_key_exists($key, $this->identityMap)) {
                    return $this->identityMap[$key];
                }
                $response = $this->restClient->get($url, $data);
                $this->identityMap[$key] = $response;
                break;
            case 'post':
                $response = $this->restClient->post($url, $data);
                break;
            case 'put':
                $response = $this->restClient->put($url, $data);
                break;
            case 'delete':
                $response = $this->restClient->delete($url, $data);
                break;
            default:
                throw new \Exception('Method not valid: ' . $method);
        }

        return $response;
    }
}