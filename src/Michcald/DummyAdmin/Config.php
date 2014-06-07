<?php

namespace Michcald\DummyAdmin;

class Config
{
    private static $instance = null;
    
    private $data = array();
    
    private function __construct() {}
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        
        return self::$instance;
    }
    
    public function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('Config file not found: ' . $filename);
        }
        
        if (!preg_match('/.*yml$/', $filename)) {
            throw new \Exception('Config file not valid: ' . $filename);
        }
        
        $content = file_get_contents($filename);
        
        try {
            $data = \Symfony\Component\Yaml\Yaml::parse($content, true);
            $this->data = array_merge_recursive($this->data, $data);
        } catch (\Exception $e) {
            throw new \Exception('da rivedere');
        }
    }
    
    public function loadDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \Exception('Directory not found: ' . $dir);
        }
        
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        foreach ($iter as $path => $d) {
            if ($d->isFile()) { // isDir()
                $this->loadFile($d);
            }
        }
    }

    public function __isset($key)
    {
        return array_key_exists($key, $this->data);
    }
    
    public function __get($key)
    {
        if (!$this->__isset($key)) {
            throw new \Exception('Config attribute not found: ' . $key);
        }
        
        return $this->data[$key];
    }
    
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    public function getData()
    {
        return $this->data;
    }
}