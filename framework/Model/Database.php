<?php

namespace Framework\Model;

use Framework\Logger\Logger;
use PDO;

/**
 * Class Database сиглтон, предоставляющий соединение с БД во время работы приложения
 * @package Framework\Model
 */
class Database
{

    private static $connection;
    private static $instance;

    private static $log;

    /**
     * Database constructor.
     * @param array $dns_params параметры соединения dns, user, password в виде ассоциативного массива.
     */
    public function __construct($dns_params)
    {
        self::$log = Logger::getLogger();
        try {
            $user = $dns_params["user"];
            $dns = $dns_params["dns"];
            $pdo = new PDO($dns, $user, $dns_params["password"]);
            self::$connection = $pdo;
            self::$log->debug("Initialized PDO : $dns@$user");
        } catch (\PDOException $ex) {
            echo "Error message " . $ex->getMessage();
        }
    }

    /**
     * Возвращает класс-синглтон для работы с подключением
     * @param array $dns_params параметры соединения dns, user, password в виде ассоциативного массива
     * @return Database инстанс класса
     */
    public static function getInstance($dns_params)
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database($dns_params);
        }
        return self::$instance;
    }

    /**
     * Метод для получения PDO объекта (уже настроен для работы с БД)
     * @return PDO возвращает PDO объект для работы с БД
     */
    public function getConnection()
    {
        return self::$connection;
    }

    private function __clone()
    {
    }

    /**
     * Закрывает соединение с БД
     */
    public function closeConnection()
    {
        self::$connection = null;
        self::$log->debug("Closed PDO");
    }


}