<?php

namespace Framework\Controller;

use Framework\Request\Request;
use Framework\Response\ResponseRedirect;

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


    /**
     * Метод выполняет редирект по заданному адресу с заданным сообщением
     * @param string $route uri для редиректа
     * @param string $message сообщение редиректа (будет отправлено как GET параметр с key = redirectmessage)
     * @return ResponseRedirect респонс-редирект на заданный uri с заданным сообщением
     */
    public function redirect($route, $message = "")
    {
        return new ResponseRedirect($route, $message);
    }


    public function generateRoute($routeName, $params = array())
    {
        return true;
    }

}