<?php

//инициализация лоадера-синглтона
Loader::getInstance();

class Loader
{
    //namespace и путь к классам framework
    const FRAMEWORK_NAMESPACE_NAME = "Framework\\";
    const FRAMEWORK_PATH = __DIR__ . "\\..\\framework";

    private static $instance;
    private static $namespaces;

    private function __construct()
    {
        //зарегистрируем классы фреймворка
        self::addNamespacePath(self::FRAMEWORK_NAMESPACE_NAME, self::FRAMEWORK_PATH);

        //регистрируем функцию для поиска классов
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

    /**
     * Возвращает лоадер-синглтон
     * @return Loader экземпляр лоадера
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Loader();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    /**
     * Сообщает автолоадеру, по какому пути искать классы с указанным namespace
     * @param $namespaceName namespace класса, с "/" в конце
     * @param $namespacePath полный путь к директории, где лежат классы
     */
    public static function addNamespacePath($namespaceName, $namespacePath)
    {
        self::$namespaces[$namespaceName] = $namespacePath;
    }

    /**
     * Возвращет все ранее добавленные namespaces
     * @return mixed ассоциативный массив namepspace => путь
     */
    public static function getNamespaces()
    {
        return self::$namespaces;
    }
}
