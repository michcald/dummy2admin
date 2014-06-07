<?php

namespace Michcald\DummyAdmin\ViewHelper;

class Config extends \Michcald\Mvc\View\Helper
{
    public function execute()
    {
        return \Michcald\DummyAdmin\Config::getInstance();
    }

}