<?php

namespace Framework\Request;

class Request
{

    public function isPost()
    {
        return true;
    }

    public function post($name)
    {
        return "";
    }
}