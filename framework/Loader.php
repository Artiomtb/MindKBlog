<?php

Loader::getInstance();

class Loader
{
    const FRAMEWORK_NAMESPACE_NAME = "Framework\\";
    const FRAMEWORK_PATH = __DIR__ . "\\..\\framework";

    private static $instance;
    private static $namespaces;

    private function __construct()
    {
        self::addNamespacePath(self::FRAMEWORK_NAMESPACE_NAME, self::FRAMEWORK_PATH);
        spl_autoload_register(function ($className) {
            foreach (self::$namespaces as $namespaceName => $namespacePath) {
                $path = $namespacePath . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, str_replace($namespaceName, "", $className)) . ".php";
                if (file_exists($path)) {
                    include_once($path);
                    break;
                }
            }
        });
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
        self::$namespaces[$namespaceName] = $namespacePath;
    }

    public static function getNamespaces()
    {
        return self::$namespaces;
    }
}
