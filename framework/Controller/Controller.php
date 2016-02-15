<?php

namespace Framework\Controller;

use Framework\DI\Service;
use Framework\Exception\HttpNotFoundException;
use Framework\Renderer\Renderer;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Response\ResponseRedirect;

/**
 * Общий класс-контроллер
 * @package    Framework\Controller
 */
class Controller
{
    private $request;

    private static $logger;

    /**
     * Конструктор контроллера
     * @param Request $request реквест
     */
    public function __construct($request)
    {
        $this->request = $request;
        self::$logger = Service::get("logger");
    }

    /**
     * Метод получает имя view, преобразует его в абсолютный путь, получает от рендерера сформированный контент и возвращает респонс. Так же, при необходимости, оборачивает в шаблон
     * @param string $view имя вью
     * @param array $params параметры, которые необходимо передать рендереру
     * @param bool $with_layout указывает, нужно ли оборачивать контент в шаблон (по умолчанию - да)
     * @return Response ответ
     * @throws HttpNotFoundException если вью не найдена
     */
    public function render($view, $params, $with_layout = true)
    {
        $view_path = $this->generatePathToView($view);
        self::$logger->debug("Rendering view $view " . (($with_layout == true) ? "with" : "without") . " layout");
        if (file_exists($view_path)) {
            $response = Renderer::render($view_path, $params);
            if ($with_layout == true && file_exists($layout_path = Service::get("config")["main_layout"])) {
                $response_with_layout = Renderer::render($layout_path, array("content" => $response));
                $response = $response_with_layout;
            }
            return new Response($response);
        } else {
            self::$logger->error("View $view_path does not exists");
            throw new HttpNotFoundException('Page Not Found!');
        }
    }

    /**
     * Метод возвращает реквест
     * @return Request реквест
     */
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

    /**
     * Возвращает путь по заданному имени роута и параметрам
     * @param string $route_name имя роута как в конфиге
     * @param array $params необязательный параметр - ассоциативный массив в формате имя переменной => значение
     * @return string uri согласно паттерну заданного роута с учетом значений параметров. Если роут не найден - вернется значение /
     */
    public function generateRoute($route_name, $params = array())
    {
        return Service::get("router")->generateRoute($route_name, $params);
    }

    /**
     * Возвращает абсолютный путь к view файлу по его имени, используя имя контроллера
     * @param string $view имя view файла
     * @return string абсолютный путь к view файлу
     */
    private function generatePathToView($view)
    {
        self::$logger->debug("Looking for view file by name $view");
        $file = debug_backtrace()[1]["file"];
        $pos = strrpos($file, DIRECTORY_SEPARATOR, -1);
        $before = substr($file, 0, $pos);
        $after = substr($file, $pos + 1);
        $controller_name = substr($after, 0, strpos($after, "Controller.php"));
        $view_file = $before . "/../views/" . $controller_name . "/" . $view . ".php";
        self::$logger->debug("Matched view $view_file");
        return $view_file;
    }
}