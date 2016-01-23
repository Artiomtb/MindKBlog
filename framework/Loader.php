<?php

Loader::getInstance();

class Loader
{
    const FRAMEWORK_NAMESPACE_NAME = "Framework\\";
    const FRAMEWORK_PATH = "\\..\\framework";

    private static $instance;

    private function __construct()
    {
        self::addNamespacePath(self::FRAMEWORK_NAMESPACE_NAME, self::FRAMEWORK_PATH);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Loader();
        }
        return self::$instance;
    }


    public static function addNamespacePath($namespaceName, $namespacePath)
    {
        spl_autoload_register(function ($className) use ($namespaceName, $namespacePath) {
            if (strrpos($className, $namespaceName) === 0) {
                include_once($namespacePath . substr($className, strpos($namespaceName, "\\")) . ".php");;
            }
        });
    }
}
