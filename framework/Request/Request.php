<?php

namespace Framework\Request;

class Request
{

    public $method;
    public $uri;
    public $post;
    public $get;

    private function __construct($method_p, $uri_p, $post_p, $get_p)
    {
        $this->method = $method_p;
        $this->uri = $uri_p;
        $this->post = $post_p;
        $this->get = $get_p;
        return $this;
    }

    public static function create()
    {
        print_r($_SERVER);
        print_r($_GET);
        print_r($_POST);
        return new Request($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $_POST, $_GET);
    }


    public function isPost()
    {
        return $this->method == "POST";
    }

    public function post($name)
    {
        return $this->post[$name];
    }

    public function getUri()
    {
        return $this->uri;
    }
}