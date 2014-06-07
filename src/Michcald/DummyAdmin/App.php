<?php

namespace Michcald\DummyAdmin;

class App
{
    private $endpoint;
    
    private $name;
    
    private $password;
    
    private $restClient;
    
    private $auth;
    
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
    
    public function call($method, $uri, $data = array())
    {
        $this->auth->setUsername($this->name)
            ->setPassword($this->password);
        
        $this->restClient->setAuth($this->auth);
        
        $url = $this->endpoint . '/' . $uri;
        
        switch ($method) {
            case 'get':
                $response = $this->restClient->get($url, $data);
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