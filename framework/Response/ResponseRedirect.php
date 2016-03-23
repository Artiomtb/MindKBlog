<?php


namespace Framework\Response;

use Framework\DI\Service;

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
        self::$logger = Service::get("logger");
        if ($message !== "") {
            Service::get("session")->set("flush", $message);
        }
        self::$logger->debug("Redirecting to $route ...");
        parent::__construct("", ResponseType::MOVED_PERMANENTLY);
        parent::addHeader("Location", $route);
    }
}