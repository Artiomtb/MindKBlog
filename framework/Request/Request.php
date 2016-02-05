<?php

namespace Framework\Request;

/**
 * Class Request класс для работы с реквестом
 * @package Framework\Request
 */
class Request
{

    //TODO добавить фильтрацию переменных

    /**
     * Приватный конструктор для реквеста.
     */
    private function __construct()
    {
    }

    /**
     * Статическая функция для создания ревеста из глобальных серверных переменных
     * @return Request экземпляр реквеста
     */
    public static function create()
    {
        return new Request();

    }

    /**
     * Проверяет, является ли метод данного реквеста = POST
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER["REQUEST_METHOD"] == "POST";
    }

    /**
     * Возвращает значение переменной POST запроса по ключу
     * @param $name string ключ
     * @return mixed значение переменной
     */
    public function post($name)
    {
        return $_POST[$name];
    }

    /**
     * Возвращает значение переменной GET запроса по ключу
     * @param $name string ключ
     * @return mixed значение переменной
     */
    public function get($name)
    {
        return $_GET[$name];
    }

    /**
     * Возвращает uri текущего реквеста
     * @return string uri реквеста
     */
    public function getUri()
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function getMethod() {
        return $_SERVER["REQUEST_METHOD"];
    }
}