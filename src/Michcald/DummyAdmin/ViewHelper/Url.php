<?php

namespace Michcald\DummyAdmin\ViewHelper;

class Url extends \Michcald\Mvc\View\Helper
{
    public function execute()
    {
        $uri = $this->getArg(0);
        
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        
        $url .= str_replace('pub/index.php', '', $_SERVER['PHP_SELF']);
        
        $url .= $uri;
        
        return $url;
    }

}