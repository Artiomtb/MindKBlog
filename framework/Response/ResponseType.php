<?php

namespace Framework\Response;

/**
 * Class ResponseType абстрактный класс хранящий константы HTTP ответов сервера
 * @package framework\Response
 */
abstract class ResponseType
{
    const OK = "200";
    const CREATED = "201";

    const MOVED_PERMANENTLY = "301";
    const MOVED_TEMPORARILY = "302";

    const UNAUTHORIZED = "401";
    const FORBIDDEN = "403";
    const NOT_FOUND = "404";

    const INTERNAL_SERVER_ERROR = "500";
    const BAD_GATEWAY = "502";
    const SERVICE_UNAVAILABLE = "503";
}