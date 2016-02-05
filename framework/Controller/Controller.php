<?php

namespace Framework\Controller;

use Framework\Request\Request;

class Controller
{


    private $request;


    /**
     * Controller constructor.
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function render($view, $params)
    {
        return true;
    }

    public function getRequest()
    {
        return $this->request;
    }


    public function redirect($route, $message = "")
    {
        return ""; //выдать респонс
    }


    public function generateRoute($routeName, $params = array())
    {
        return true;
    }

}