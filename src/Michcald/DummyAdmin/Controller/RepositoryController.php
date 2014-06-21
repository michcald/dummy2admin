<?php

namespace Michcald\DummyAdmin\Controller;

class RepositoryController extends \Michcald\Mvc\Controller\HttpController
{
    public function listAction($repository)
    {
        $page = (int)$this->getRequest()->getQueryParam('page', 1);
        $limit = (int)$this->getRequest()->getQueryParam('limit', 100);
        $query = $this->getRequest()->getQueryParam('query', false);
        $orderBy = $this->getRequest()->getQueryParam('orderb', false);
        $orderDir = $this->getRequest()->getQueryParam('orderd', false);
        $filters = $this->getRequest()->getQueryParam('filters', array());

        $app = \Michcald\Mvc\Container::get('dummy.app');

        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);

        $resp = $app->call('get', $repository, array(
            'page'    => $page,
            'limit'   => $limit,
            'query'   => $query,
            'orderb'  => $orderBy,
            'orderd'  => $orderDir,
            'filters' => $filters
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
                'limit'      => $limit,
                'query'      => $query,
                'orderb'     => $orderBy,
                'orderd'     => $orderDir,
                'filters'    => $filters
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

        $errors = null;

        if ($this->getRequest()->isMethod('POST')) {

            $data = $this->getRequest()->getData();

            $dataFiltered = $data;

            // file managing
            foreach ($data as $key => $value) {
                if (isset($data[$key]['tmp_name'])) {
                    if ($data[$key]['tmp_name'] != '') {
                        $filename = 'tmp/' . $data[$key]['name'];

                        move_uploaded_file(
                            $data[$key]['tmp_name'],
                            $filename
                        );

                        $dataFiltered[$key] = '@' . $filename;
                    } else {
                        $dataFiltered[$key] = null;
                    }
                }
            }

            $resp = $app->call('put', $repository . '/' . $id, $dataFiltered);
            $statusCode = $resp->getStatusCode();
            $json = $resp->getContent();
            $respJson = json_decode($json, true);

            //\Zend\Debug\Debug::dump($respJson);die;

            if ($resp->getStatusCode() == 400) {
                $errors = $respJson['error'];
            } else if ($resp->getStatusCode() == 200) {
                $success = true;
            } else {
                $success = false;
            }

            $this->emptyTmpDir();

        } else {
            $dataFiltered = $entity;
        }

        $dataFiltered['id'] = $id;

        $page = $this->getView()->render(
            '../app/views/repository/edit.phtml',
            array(
                'repository' => $repoInfo,
                'entity'     => $dataFiltered,
                'error'      => $errors,
                'success'    => isset($success) ? true : false
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
        $app = \Michcald\Mvc\Container::get('dummy.app');

        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);

        $resp = $app->call('get', $repository . '/' . $id);
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $entity = json_decode($json, true);

        if ($this->getRequest()->isMethod('POST')) {
            $resp = $app->call('delete', $repository . '/' . $id);
            $statusCode = $resp->getStatusCode();
            if ($statusCode == 204) {
                return $this->listAction($repository);
            }
        }

        $page = $this->getView()->render(
            '../app/views/repository/delete.phtml',
            array(
                'repository' => $repoInfo,
                'entity'     => $entity,
                'success'    => isset($success) ? $success : null
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

    public function createAction($repository)
    {
        $app = \Michcald\Mvc\Container::get('dummy.app');

        $resp = $app->call('get', $repository . '/_info');
        $statusCode = $resp->getStatusCode();
        $json = $resp->getContent();
        $repoInfo = json_decode($json, true);

        $errors = null;

        if ($this->getRequest()->isMethod('POST')) {
            $data = $this->getRequest()->getData();

            $dataFiltered = $data;
//\Zend\Debug\Debug::dump($data);die;
            // file managing
            foreach ($data as $key => $value) {
                if (is_array($data[$key])) {
                    echo $key .'<br>';
                    if ($data[$key]['tmp_name'] != '') {
                        $filename = __DIR__ . '/../../../../pub/tmp/' . $data[$key]['name'];

                        move_uploaded_file(
                            $data[$key]['tmp_name'],
                            $filename
                        );

                        $dataFiltered[$key] = '@' . $filename;
                    } else {
                        $dataFiltered[$key] = null;
                    }
                }
            }

            $resp = $app->call('post', $repository, $dataFiltered);
            $statusCode = $resp->getStatusCode();
            $json = $resp->getContent();
            $respJson = json_decode($json, true);

            //\Zend\Debug\Debug::dump($resp);die;

            if ($resp->getStatusCode() == 400) {
                $errors = $respJson['error'];
            } else if ($resp->getStatusCode() == 200) {
                $success = true;
            } else {
                $success = false;
            }

            $this->emptyTmpDir();

        } else {
            $dataFiltered = array();
            foreach ($repoInfo['fields'] as $field) {
                if ($field['name'] == 'id') {
                    continue;
                }
                $dataFiltered[$field['name']] = null;
            }
        }

        $page = $this->getView()->render(
            '../app/views/repository/create.phtml',
            array(
                'repository' => $repoInfo,
                'entity'     => $dataFiltered,
                'error'      => $errors,
                'success'    => isset($success) ? true : false
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

    private function emptyTmpDir()
    {
        $dir = 'tmp';

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        foreach ($iter as $path => $d) {
            if ($d->isFile()) { // isDir()
                unlink($d);
            }
        }
    }
}