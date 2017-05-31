<?php

use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

class MModelFactory
{

    private $persistence;

    public function __construct($persistence)
    {
        $this->persistence = $persistence;
    }

    public function build ($modelClass, $data = NULL)
    {
//        $function = new \ReflectionClass($modelClass);
//        $modelName = strtolower($function->getShortName());
//        $proxyClassName = str_replace('models', "persistence\\maestro\\{$modelName}", $modelClass);
        if ($this->persistence == 'maestro') {
            $proxyClassName = str_replace('models', "persistence\\maestro\\models", $modelClass);
            mdump('proxy ClassName = ' . $proxyClassName);
            $proxy = new $proxyClassName();
            $proxy->onCreate($data);
        } else {
            mdump('proxy ClassName = ' . $modelClass);
            $proxy = new $modelClass();
        }
        return $proxy;
    }


}