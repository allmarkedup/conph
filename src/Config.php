<?php namespace Amu\Conph;

use Amu\Conph\Helper;

class Config
{
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function merge($config = [])
    {
        $this->config = Helper::merge($this->config, $config);
    }

    public function get($path)
    {
        return $this->applyGetter($path);
    }

    public function set($path, $value)
    {
        $this->applySetter($path, $value);
    }

    protected function getConfigItem($path)
    {
        return Helper::get($this->config, $path);
    }

    protected function setConfigItem($path, $value)
    {
        Helper::set($this->config, $path, $value);
    }

    protected function applyGetter($path)
    {
        $value = $this->getConfigItem($path);
        $getterName = $this->convertPathToFunctionName('get', $path);
        if ( method_exists($this, $getterName) ) {
            return $this->$getterName($value);
        }
        return $value;
    }

    protected function applySetter($path, $value)
    {
        $setterName = $this->convertPathToFunctionName('set', $path);
        if ( method_exists($this, $setterName) ) {
            return $this->$setterName($value);
        }
        $this->setConfigItem($path, $value);
    }

    protected function convertPathToFunctionName($prefix, $path)
    {
        $path = str_replace(' ', '_', ucwords(str_replace('_', ' ', $path)));
        $path = str_replace(' ', '', ucwords(str_replace('.', ' ', $path)));
        return $prefix . $path;
    }

}