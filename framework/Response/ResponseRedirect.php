<?php


namespace Framework\Response;

/**
 * Class ResponseRedirect класс респонса, отваечающий за перенаправление
 * @package Framework\Response
 */
class ResponseRedirect extends Response
{

    /**
     * ResponseRedirect constructor.
     * @param string $route путь для перенаправления
     * @param string $message сообщение, с которым будет выполнено перенаправление (добавляется в GET параметры с ключом redirectmessage)
     */
    public function __construct($route, $message = "")
    {
        $route = ($message == "") ? $route : $route . "?redirectmessage=" . $message;
        parent::sendHeader("Location", $route);
    }
}