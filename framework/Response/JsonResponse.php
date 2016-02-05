<?php


namespace Framework\Response;

/**
 * Class JsonResponse класс для отправки респонса типа json
 * @package Framework\Response
 */
class JsonResponse extends Response
{

    /**
     * JsonResponse constructor.
     * @param array $array_json ассоциативный массив, который будет преобразован в json
     */
    public function __construct($array_json)
    {
        parent::__construct(json_encode($array_json), ResponseType::OK, "application/json");
    }
}