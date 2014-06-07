<?php

namespace Michcald\DummyAdmin\Controller;

class IndexController extends \Michcald\Mvc\Controller\HttpController
{
    public function indexAction()
    {
        $page = $this->getView()->render(
            '../app/views/index/index.phtml'
        );
        
        $body = $this->getView()->render(
            '../app/views/layout.phtml',
            array(
                'page' => $page
            )
        );
        
        $response = new \Michcald\Mvc\Response();
        $response->setContent('text/html')
                ->setContent($body);
        
        return $response;
    }
    
    public function repositoriesAction()
    {
        $app = \Michcald\Mvc\Container::get('dummy.app');
        
        $resp = $app->call('get', '_info');
        
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repositories = json_decode($json, true);
        
        $page = $this->getView()->render(
            '../app/views/index/repositories.phtml',
            array(
                'repos' => $repositories
            )
        );
        
        $body = $this->getView()->render(
            '../app/views/layout.phtml',
            array(
                'page' => $page
            )
        );
        
        $response = new \Michcald\Mvc\Response();
        $response->setContent('text/html')
                ->setContent($body);
        
        return $response;
    }
    
    public function notFoundAction()
    {
        $page = $this->getView()->render(
            '../app/views/index/not_found.phtml'
        );
        
        $body = $this->getView()->render(
            '../app/views/layout.phtml',
            array(
                'page' => $page
            )
        );
        
        $response = new \Michcald\Mvc\Response();
        $response->setContent('text/html')
                ->setContent($body);
        
        return $response;
    }
}