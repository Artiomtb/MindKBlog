<?php


namespace Framework;

use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Router\Router;

class Application
{

    private $config;

    /**
     * Конструктор фронт контроллера
     * @param $config_path string к конфигурационному файлу
     */
    public function __construct($config_path)
    {
        $this->config = include_once $config_path;
        //TODO добавить обработку остальных параметров конфига, когда понядобятся
    }

    /**
     * Функция выполняет роутинг, запуск нужного контроллера, отдает респонс
     */
    public function run()
    {
        $router = new Router($this->config["routes"]);
        $route_answer = $router->route(Request::create());
        $route = $route_answer["route"];

        //если роут не найден по данному uri
        if (empty($route)) {
            $response = new Response("Route not found", 404);
        } else {
            $controller_class = $route["controller"];
            $method_name = $route["action"] . "Action";
            if (class_exists($controller_class) && method_exists($controller_class, $method_name)) {
                $request_params = $route_answer["params"];
                $response = $this->getResponseFromController($controller_class, $method_name, $request_params);
            } else {
                $response = new Response("Such controller and method does not exists: " . "$controller_class -> $method_name()<br/>");
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
        $controller = new $controller_name();
        if (!empty($params)) {
            return call_user_func_array(array($controller, $method_name), $params);
        } else {
            return $controller->{$method_name}();
        }
    }
}