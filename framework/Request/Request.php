<?php

namespace Framework\Request;

/**
 * Class Request класс для работы с реквестом
 * @package Framework\Request
 */
class Request
{

    public $method;
    public $uri;
    public $post;
    public $get;

    /**
     * Приватный конструктор для реквеста.
     * @param $method_p string метод
     * @param $uri_p string uri
     * @param $post_p array параметры POST
     * @param $get_p array параметры GET
     */
    private function __construct($method_p, $uri_p, $post_p, $get_p)
    {
        $this->method = $method_p;
        $this->uri = $uri_p;
        $this->post = $post_p;
        $this->get = $get_p;
        return $this;
    }

    /**
     * Статическая функция для создания ревеста из глобальных серверных переменных
     * @return Request экземпляр реквеста
     */
    public static function create()
    {
        return new Request($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $_POST, $_GET);

    }

    /**
     * Проверяет, является ли метод данного реквеста = POST
     * @return bool
     */
    public function isPost()
    {
        return $this->method == "POST";
    }

    /**
     * Возвращает значение переменной POST запроса по ключу
     * @param $name string ключ
     * @return mixed значение переменной
     */
    public function post($name)
    {
        return $this->post[$name];
    }

    /**
     * Возвращает значение переменной GET запроса по ключу
     * @param $name string ключ
     * @return mixed значение переменной
     */
    public function get($name)
    {
        return $this->get[$name];
    }

    /**
     * Возвращает uri текущего реквеста
     * @return string uri реквеста
     */
    public function getUri()
    {
        return $this->uri;
    }
}