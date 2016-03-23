<?php

namespace Framework\Session;

class Session
{
    public $returnUrl;

    public function __construct()
    {
        session_start();
    }

    public function get($key)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
    }

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

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function remove($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public function getAll()
    {
        return $_SESSION;
    }

    function __get($name)
    {
        // TODO: Implement __get() method.
    }


}