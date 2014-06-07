<?php

namespace Michcald\DummyAdmin\Controller;

class RepositoryController extends \Michcald\Mvc\Controller\HttpController
{
    private $baseUrl = 'http://localhost/dummy2/dummy2/api';
    
    public function listAction($repository)
    {
        $page = (int)$this->getRequest()->getQueryParam('page', 1);
        $query = $this->getRequest()->getQueryParam('query', false);
        $orderBy = $this->getRequest()->getQueryParam('orderb', false);
        $orderDir = $this->getRequest()->getQueryParam('orderd', false);
        
        $app = \Michcald\Mvc\Container::get('dummy.app');
        
        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);
        
        $resp = $app->call('get', $repository, array(
            'page'   => $page,
            'query'  => $query,
            'orderb' => $orderBy,
            'orderd' => $orderDir
        ));
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $list = json_decode($json, true);
        
        //\Zend\Debug\Debug::dump($resp);die;
        
        $page = $this->getView()->render(
            '../app/views/repository/list.phtml',
            array(
                'repository' => $repoInfo,
                'list'       => $list,
                'query'      => $query
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
    
    public function readAction($repository, $id)
    {
        $app = \Michcald\Mvc\Container::get('dummy.app');
        
        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);
        
        $resp = $app->call('get', $repository . '/' . $id);
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $entity = json_decode($json, true);
        
        //\Zend\Debug\Debug::dump($entity);die;
        
        $page = $this->getView()->render(
            '../app/views/repository/read.phtml',
            array(
                'repository' => $repoInfo,
                'entity'     => $entity
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
    
    public function editAction($repository, $id)
    {
        $app = \Michcald\Mvc\Container::get('dummy.app');
        
        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);
        
        $resp = $app->call('get', $repository . '/' . $id);
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $entity = json_decode($json, true);
        
        //\Zend\Debug\Debug::dump($entity);die;
        
        $page = $this->getView()->render(
            '../app/views/repository/edit.phtml',
            array(
                'repository' => $repoInfo,
                'entity'     => $entity
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
    
    public function deleteAction($repository, $id)
    {
        $client = new \Michcald\RestClient\Client();
        
        $auth = new \Michcald\RestClient\Auth\Basic();
        $auth->setUsername('stefano')
                ->setPassword('123456');
        $client->setAuth($auth);
        
        $resp = $client->delete($this->baseUrl . "/{$repository}/{$id}");
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $info = json_decode($json, true);
        
        return $this->listAction($repository);
    }
    
    public function createAction($repository)
    {
        $client = new \Michcald\RestClient\Client();
        
        $auth = new \Michcald\RestClient\Auth\Basic();
        $auth->setUsername('stefano')
                ->setPassword('123456');
        $client->setAuth($auth);
        
        $resp = $client->get($this->baseUrl . "/{$repository}/_info");
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $info = json_decode($json, true);
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $data = $this->getRequest()->getData();
            
            // TODO for every file
            $filename = 'tmp/' . $data['img']['name'];
            
            move_uploaded_file(
                $data['img']['tmp_name'], 
                $filename
            );
            
            $data['img'] = '@' . $filename;
            
            $resp = $client->post(
                $this->baseUrl . "/{$repository}",
                $data
            );
            
            if ($resp->getStatusCode() != 200) {
                var_dump(json_decode($resp->getContent(), true));
                die;
            }
            
            unlink($filename);
            
            var_dump(json_decode($resp->getContent(), true));
            die('ok');
            
        } else {
            $body = $this->getView()->render(
                'views/repository/create.phtml',
                array(
                    'info' => $info
                )
            );
        }
        //\Zend\Debug\Debug::dump($info);die;
        
        $response = new \Michcald\Mvc\Response();
        $response->setContent('text/html')
                ->setContent($body);
        
        return $response;
    }
}