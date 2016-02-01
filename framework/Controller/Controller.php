<?php

namespace Framework\Controller;

use Framework\Request\Request;

class Controller
{

    public function render($view, $params)
    {
        return true;
    }

    public function getRequest()
    {
        return "";
    }


    public function redirect($route, $message="")
    {
        return "";
    }


    public function generateRoute($routeName)
    {
        return true;
    }

}