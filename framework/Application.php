<?php


namespace Framework;

use Framework\DI\Service;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Response\ResponseType;
use Framework\Router\Router;

class Application
{

    private $config;
    private $request;

    /**
     * Конструктор фронт контроллера
     * @param $config_path string к конфигурационному файлу
     */
    public function __construct($config_path)
    {
        $config = include_once $config_path;
        $this->setErrorReportingLevel($config["mode"]);
        Service::setAll($config["di"]);
        $this->config = $config;

        //TODO добавить обработку остальных параметров конфига, когда понядобятся
    }

    /**
     * Функция выполняет роутинг, запуск нужного контроллера, отдает респонс
     */
    public function run()
    {
        $router = new Router($this->config["routes"]);
        $this->request = Request::create();
        $route_answer = $router->route($this->request);
        $route = $route_answer["route"];

        //если роут не найден по данному uri
        if (empty($route)) {
            $response = new Response("Route not found", ResponseType::NOT_FOUND);
        } else {
            $controller_class = $route["controller"];
            $method_name = $route["action"] . "Action";
            if (class_exists($controller_class) && method_exists($controller_class, $method_name)) {
                $request_params = $route_answer["params"];
                $response = $this->getResponseFromController($controller_class, $method_name, $request_params);

//                TODO добавить оборачивание респонса в шаблон
//                if("text/html" === $response->getContentType()) {
//                    $content = $response->getContent();
//                    $response = new Response( include (__DIR__.'/../../src/Blog/views/500.html.php'));
//                }
//                $response->setContent($response->getContent())

            } else {
                $response = new Response("Such controller and method does not exists: " . "$controller_class -> $method_name()", ResponseType::NOT_FOUND);
            }
        }
        $response->send();
    }

    /**
     * @param $controller_name string имя класса-контроллера, который необходимо запустить
     * @param $method_name string имя метода в классе контроллере
     * @param array $params параметры, которые необходимо передать в метод. Если масси пустой - будет вызван метод без параметров
     * @return Response ответ сервера
     */
    private function getResponseFromController($controller_name, $method_name, $params = array())
    {
        $controller = new $controller_name($this->request);
        if (!empty($params)) {
            //TODO добавить биндинг переменных по имени
            return call_user_func_array(array($controller, $method_name), $params);
        } else {
            return $controller->{$method_name}();
        }
    }

    /**
     * Возвращает конфиг текущего приложения
     * @return array конфиг текущего приложения в виде ассоциативного массива
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Устанавливает уровень вывода ошибок в зависимости от типа среды, на которой запущено приложение
     * @param string $mode если = prod, отключается запись всех ошибок, если dev - максимальная запись. Иначе - ошибки и предупреждения
     */
    private function setErrorReportingLevel($mode)
    {
        if ("prod" == $mode) {
            error_reporting(0);
        } elseif ("dev" == $mode) {
            error_reporting(E_ALL);
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
        }
    }
}