<?php

namespace Michcald\DummyAdmin\Event\Listener;

class Auth implements \Michcald\Mvc\Event\SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'mvc.event.dispatch.pre' => 'auth'
        );
    }
    
    public function auth($event)
    {
        $request = $event->get('mvc.request');
        
        if ($request->isMethod('cli')) {
            return;
        }
        
        $user = $request->getHeader('PHP_AUTH_USER', false);
        $password = $request->getHeader('PHP_AUTH_PW', false);
        
        $config = \Michcald\DummyAdmin\Config::getInstance();
        
        foreach ($config->users as $auth) {
            if ($auth['username'] == $user && $auth['password'] == $password) {
                return;
            }
        }
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
    
        $response = new \Michcald\Mvc\Response();
        $response->setStatusCode(401)
            ->setContent('Not authorized');
        $response->send();
        die();
    }

}