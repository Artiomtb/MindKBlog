<?php


class Loader
{
    const FRAMEWORK_NAMESPACE_NAME = "Framework\\";
    const FRAMEWORK_PATH = "\\..\\framework";

    private static $frameworkClassesRegistered = false;

    public static function addNamespacePath($namespaceName, $namespacePath)
    {
        if (!self::$frameworkClassesRegistered) {
            self::register_classes(self::FRAMEWORK_NAMESPACE_NAME, self::FRAMEWORK_PATH);
            self::$frameworkClassesRegistered = true;
        }
        self::register_classes($namespaceName, $namespacePath);
    }

    private static function register_classes($namespaceName, $namespacePath)
    {
        spl_autoload_register(function ($className) use ($namespaceName, $namespacePath) {
            if (strrpos($className, $namespaceName) === 0) {
                include_once($namespacePath . substr($className, strpos($namespaceName, "\\")) . ".php");;
            }
        });
    }
}
