<?php

namespace Framework\Session;

/**
 * Класс для работы с сессией
 * @package Framework\Session
 */
class Session
{
    /**
     * Конструктор класса сессии
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * Позволяет получить значение из сессии по ключу
     * @param string $key ключ
     * @return object|null значение или null, если объект не найден
     */
    public function get($key)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
    }

    /**
     * Позволяет получить значение из сессии по ключу и сразу же удалить его
     * @param string $key ключ
     * @return object|null значение или null, если объект не найден
     */
    public function getAndRemove($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            $result = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Устанавливает значение в сессию по ключу
     * @param string $key ключ
     * @param object $value значение
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Удаляет значение из сессии по ключу
     * @param string $key ключ
     */
    public function remove($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Возвращает все пары ключ-значение из сессии в формате ассоциативного массива
     * @return array
     */
    public function getAll()
    {
        return $_SESSION;
    }

    public function __get($name)
    {
        $this->get($name);
    }

    function __set($name, $value)
    {
        $this->set($name, $value);
    }

}