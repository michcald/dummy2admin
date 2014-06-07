<?php

include '../vendor/autoload.php';

$config = \Michcald\DummyAdmin\Config::getInstance();
$config->loadDir('../app/config');

if ($config->env == 'dev') { // forse fare dev.php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$mvc = new \Michcald\Mvc\Mvc();

foreach ($config->routes as $routeConfig) {
    
    $uri = new \Michcald\Mvc\Router\Route\Uri();
    $uri->setPattern($routeConfig['uri']['pattern']);

    foreach ($routeConfig['uri']['requirements'] as $requirement) {
        $uri->setRequirement($requirement['param'], $requirement['value']);
    }
    
    $route = new \Michcald\Mvc\Router\Route();
    $route->setMethods($routeConfig['methods'])
        ->setUri($uri)
        ->setId($routeConfig['name'])
        ->setControllerClass($routeConfig['controller'])
        ->setActionName($routeConfig['action']);

    $mvc->addRoute($route);
}

$mvc->addEventSubscriber(new \Michcald\DummyAdmin\Event\Listener\Auth());

$view = \Michcald\Mvc\Container::get('mvc.view');
$view->addHelper('\\Michcald\\DummyAdmin\\ViewHelper\\Url', 'url')
    ->addHelper('\\Michcald\\DummyAdmin\\ViewHelper\\Config', 'config')
    ->addHelper('\\Michcald\\DummyAdmin\\ViewHelper\\Repositories', 'repositories');


$app = new Michcald\DummyAdmin\App();
$app->setEndpoint($config->dummy['endpoint'])
    ->setName($config->dummy['app']['name'])
    ->setPassword($config->dummy['app']['password']);

\Michcald\Mvc\Container::add('dummy.app', $app);

$request = new Michcald\DummyAdmin\Request();

$mvc->run($request);






