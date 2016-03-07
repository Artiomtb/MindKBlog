<?php


namespace Framework;

use Framework\DI\Service;
use Framework\Logger\Logger;
use Framework\Model\Database;
use Framework\Request\Request;
use Framework\Response\Response;
use Framework\Response\ResponseType;
use Framework\Router\Router;


class Application
{

    private $config;
    private $request;
    private $router;

    private static $logger;

    private $pdo;

    /**
     * Конструктор фронт контроллера
     * @param $config_path string к конфигурационному файлу
     */
    public function __construct($config_path)
    {
        $config = include_once $config_path;
        $run_mode = $config["mode"];
        self::$logger = Logger::getLogger($this->configureLogParams($run_mode, $config["log"]));
        Service::set("logger", self::$logger);
        self::$logger->debug("Run mode set to " . $run_mode);
        $this->setErrorReportingLevel($run_mode);
        $this->router = new Router($config["routes"]);
        $this->pdo = Database::getInstance($config["pdo"]);
        Service::setAll($config["di"]);
        Service::set("router", $this->router);
        Service::set("pdo", $this->pdo->getConnection());
        Service::set("config", $config);
        $this->config = $config;

        //TODO добавить обработку остальных параметров конфига, когда понядобятся
    }

    /**
     * Функция выполняет роутинг, запуск нужного контроллера, отдает респонс
     */
    public function run()
    {
        self::$logger->debug("Running application...");

        $this->request = Request::create();
        $route_answer = $this->router->route($this->request);
        $route = $route_answer["route"];

        //если роут не найден по данному uri
        if (empty($route)) {
            self::$logger->warn("Router was not found");
            $response = new Response("Route not found", ResponseType::NOT_FOUND);
        } else {
            $controller_class = $route["controller"];
            $method_name = $route["action"] . "Action";
            if (class_exists($controller_class) && method_exists($controller_class, $method_name)) {
                $request_params = $route_answer["params"];
                $response = $this->getResponseFromController($controller_class, $method_name, $request_params);
            } else {
                self::$logger->error("Such controller and method does not exists: " . "$controller_class -> $method_name()");
                $response = new Response("Such controller and method does not exists: " . "$controller_class -> $method_name()", ResponseType::NOT_FOUND);
            }
        }
        $this->pdo->closeConnection();
        $response->send();

    }

    /**
     * @param $controller_name string имя класса-контроллера, который необходимо запустить
     * @param $method_name string имя метода в классе контроллере
     * @param array $params параметры, которые необходимо передать в метод. Если массив пустой - будет вызван метод без параметров
     * @return Response ответ сервера
     */
    private function getResponseFromController($controller_name, $method_name, $params = array())
    {
        $ordered_params = array();

        //получим все переменные нужного метода контроллера
        $reflection = new \ReflectionClass($controller_name);
        $method_params = $reflection->getMethod($method_name)->getParameters();

        //перестроим входящие переменные в порядке их следования в методе в массиве $ordered_params. Если какая-то переменная не пришла - подставим пустое значение
        foreach ($method_params as $param) {
            $current_value = "";
            $cur_param_name = $param->name;
            if (array_key_exists($cur_param_name, $params)) {
                $current_value = $params[$cur_param_name];
            }
            $ordered_params[] = $current_value;
        }
        $controller = new $controller_name($this->request);

        self::$logger->debug("Calling $controller_name->$method_name(" . implode(", ", $ordered_params) . ")");
        $response = call_user_func_array(array($controller, $method_name), $ordered_params);
        self::$logger->debug("Got response: " . $response);
        return $response;
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

    /**
     * Дополняет настройки логгирования error_level и backtrace_enabled согласно mode запуска приложения, если они не указаны в конфиге). Если prod - уровень логгирования будет выставлен error, backtrace отлючен. Если dev - уровень логгирования будет выставлен debug, backtrace включен.
     * @param string $mode тип запуска приложения
     * @param array $log_params параметры логгирования (полученные из конфига)
     * @return array скорректированные параметры логгирования
     */
    private function configureLogParams($mode, $log_params)
    {
        if (!array_key_exists("error_level", $log_params)) {
            if ("prod" == $mode) {
                $log_params["error_level"] = "error";
            } elseif ("dev" == $mode) {
                $log_params["error_level"] = "debug";
            }
        }
        if (!array_key_exists("backtrace_enabled", $log_params)) {
            if ("prod" == $mode) {
                $log_params["backtrace_enabled"] = false;
            } elseif ("dev" == $mode) {
                $log_params["backtrace_enabled"] = true;
            }
        }
        return $log_params;
    }
}