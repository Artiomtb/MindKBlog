<?php


namespace Framework\Response;
use Framework\Logger\Logger;

/**
 * Class ResponseRedirect класс респонса, отваечающий за перенаправление
 * @package Framework\Response
 */
class ResponseRedirect extends Response
{

    private static $logger;

    /**
     * ResponseRedirect constructor.
     * @param string $route путь для перенаправления
     * @param string $message сообщение, с которым будет выполнено перенаправление (добавляется в GET параметры с ключом redirectmessage)
     */
    public function __construct($route, $message = "")
    {
        $route = ($message == "") ? $route : $route . "?redirectmessage=" . $message;
        self::$logger = Logger::getLogger();
        self::$logger->debug("Redirecting to $route ...");
        parent::sendHeader("Location", $route);
        exit;
    }
}