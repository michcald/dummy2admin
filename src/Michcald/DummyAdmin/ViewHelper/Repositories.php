<?php

namespace Michcald\DummyAdmin\ViewHelper;

class Repositories extends \Michcald\Mvc\View\Helper
{
    public function execute()
    {
        $app = \Michcald\Mvc\Container::get('dummy.app');
        
        $resp = $app->call('get', '_info');
        
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repositories = json_decode($json, true);
        
        return $repositories;
    }

}