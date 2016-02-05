<?php


namespace Framework\Response;


class ResponseRedirect extends Response
{

    /**
     * ResponseRedirect constructor.
     */
    public function __construct($route, $message = "")
    {
        $route = ($message == "") ? $route : $route . "?redirectmessage=" . $message;
        parent::addHeader("Location", $route);
    }
}