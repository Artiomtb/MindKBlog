<?php


namespace Framework\Response;

/**
 * Class Response ответ, посылаемый на запрос
 * @package Framework\Response
 */
class Response
{

    private $content;
    private $response_code;
    private $content_type;

    /**
     * Response constructor.
     * @param string $content контент, который вернется в ответе на запрос
     * @param string $response_code HTTP код ответа, 200 по умолчанию
     * @param string $content_type HTTP тип коннтенат ответа, по умолчанию - text/html
     */
    public function __construct($content, $response_code = ResponseType::OK, $content_type = "text/html")
    {
        $this->content = $content;
        $this->response_code = $response_code;
        $this->content_type = $content_type;
    }

    /**
     * Отправляет текущий респонс (является комбинацией sendHeaders() и sendContent())
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Функция отправляет значения кода ответа и типа контента, которые заданы для текушего респонса
     */
    public function sendHeaders()
    {
        header("HTTP/1.1 " . $this->response_code);
        header("Content-Type: " . $this->content_type);
    }

    /**
     * Функция отправляет текущий контент для репонса
     */
    public function sendContent()
    {
        echo $this->content;
    }

    /**
     * Возврашает контент респонса
     * @return string контент
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Устанавливает контент для текущего респонса
     * @param string $content новый контент
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Возвращает код ответа текущего респонса
     * @return string код ответа текущего респонса
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * Устанавливает код ответа для текущего респонса
     * @param string $response_code новый код ответа
     */
    public function setResponseCode($response_code)
    {
        $this->response_code = $response_code;
    }

    /**
     * Возврашает тип контента текущего респонса
     * @return string тип контента
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * Устанавливает тип контента для текущего респонса
     * @param string $content_type новый тип контента
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }
}