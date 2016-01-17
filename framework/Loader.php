<?php

const FRAMEWORK_NAMESPACE_NAME = "Framework\\";
class Loader
{

    private static $namespacePath;
    private static $namespaceName;
    private static $frameworkClassesRegistered = false;

    public static function addNamespacePath($namespaceName, $namespacePath)
    {
        if (!self::$frameworkClassesRegistered) {
            self::registerFrameworkPath();
        }
        self::$namespacePath = $namespacePath;
        self::$namespaceName = $namespaceName;
        spl_autoload_register(function ($className) {
            if (strrpos($className, self::$namespaceName) === 0) {
                include_once(Loader::$namespacePath . substr($className, strpos($className, "\\")) . '.php');
            }
        });
    }

    private static function registerFrameworkPath()
    {
        spl_autoload_register(function ($className) {
            if (strrpos($className, FRAMEWORK_NAMESPACE_NAME) === 0) {
                include_once(substr($className, strpos(FRAMEWORK_NAMESPACE_NAME, "\\")) . '.php');;
            }
        });
        self::$frameworkClassesRegistered = true;
    }
}
