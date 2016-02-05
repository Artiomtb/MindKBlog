<?php


namespace Framework\Router;

use Framework\Request\Request;

class Router
{

    private $config_array;
    private $request;

    /**
     * Конструктор, принимает на вход конфиг роутера
     * @param $config_array array ассоциативный массив с роутами
     */
    public function __construct($config_array)
    {
        foreach ($config_array as $name => $config) {

            //добавить слеш в конец, если его там нет
            if (!$this->isEndsWithSlash($config["pattern"])) {
                $config_array[$name]["pattern"] = $config["pattern"] . "/";
            }
        }
        $this->config_array = $config_array;
    }

    /**
     * Метод, возвращающий подходящий маршрут для реквеста и его параметры
     * @param $request Request реквест
     * @return null|array
     */
    public function route($request)
    {
        $this->request = $request;
        $uri = $request->getUri();

        //отрежем параметры от uri, если они есть
        $uri = strtok($uri, "?");

        //добавить слеш в конец, если его там нет
        if (!$this->isEndsWithSlash($uri)) {
            $uri = $uri . "/";
        };

        //раскладываем uri в массив
        $parsedUri = explode("/", $uri);

        $matched_config = null;
        $uri_variables = null;

        //проверяем все паттерны на соотвествие uri
        foreach ($this->config_array as $name => $config) {
            $pattern = $config["pattern"];

            //раскладываем текущий паттерн в массив
            $parsedPattern = explode("/", $pattern);

            //проверяем, подходит ли текущий паттерн по формату для uri
            $uri_variables = $this->checkAndGetVariables($parsedUri, $parsedPattern);

            //если подходит - проверить, соблюдаются ли условия, описанные в _requirements. Если да - роут найден
            if (!is_null($uri_variables) && $this->checkRequirements($config, $uri_variables)) {
                $matched_config = $config;
                break;
            }
        }

        //формируем результат работы роутера
        $result = array("route" => $matched_config,
            "params" => $uri_variables);
        return $result;
    }

    /**
     * Проверяет, соответствует ли uri паттерну
     * @param $parsedUri array разложенный в массив uri
     * @param $parsedPattern array разложенный в массив паттерн
     * @return array|null вернет null, если паттерн не соответствует uri, или ассоциативный массив в формате переменная => значение, если uri соотвествует паттерну
     */
    private function checkAndGetVariables($parsedUri, $parsedPattern)
    {
        $variables = array();
        $patternSize = sizeof($parsedPattern);

        //если размеры массивов идентичны - проверяем по элементу
        if (sizeof($parsedUri) === $patternSize) {
            for ($i = 1; $i < $patternSize - 1; $i++) {
                $curPattern = $parsedPattern[$i];

                //если текущий элемент паттерна - переменная, записать в $variables, иначе - сравнить
                if ($this->isVariable($curPattern)) {
                    $varName = substr($curPattern, 1, strlen($curPattern) - 2);
                    $variables[$varName] = $parsedUri[$i];
                } elseif ($parsedUri[$i] != $curPattern) {
                    return null;
                }
            }
            return $variables;
        } else {
            return null;
        }
    }

    /**
     * Проверяет, является ли текущая строка переменной в формате {name}
     * @param $curPattern string строка для проверки
     * @return bool true, если в строка - переменная в формате {name}
     */
    private function isVariable($curPattern)
    {
        preg_match("/^{[^{}]+}$/", $curPattern, $output_array);
        return sizeof($output_array) > 0;
    }

    //TODO удалить этот метод
    /**
     * Метод проверяет, заканчивается ли строка символом "/"
     * @param $string string строка для проверки
     * @return bool true, если строка заканчивается на "/"
     */
    private function isEndsWithSlash($string)
    {
        return substr($string, -1) === "/";
    }

    /**
     * Проверяет соблюдение _requirements конкретного конфига для uri
     * @param $matched_config array конфиг роута
     * @param $uri_variables string проверяемый uri
     * @return bool true, если условия соблюдаются
     */
    private function checkRequirements($matched_config, $uri_variables)
    {
        $result = true;
        if (array_key_exists("_requirements", $matched_config)) {
            foreach ($matched_config["_requirements"] as $reqName => $recValue) {

                //если есть ограничение по методу
                if ($reqName === "_method") {
                    if (strcmp($this->request->getMethod(), $recValue) != 0) {
                        $result = false;
                        break;
                    }
                } //проверка переменных по заданным regexp
                elseif (!is_null($uri_variables[$reqName])) {
                    preg_match("/^" . $recValue . "$/", $uri_variables[$reqName], $output_array);
                    if (sizeof($output_array) == 0) {
                        $result = false;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает путь по заданному имени роута и параметрам
     * @param string $route_name имя роута как в конфиге
     * @param array $params необязательный параметр - ассоциативный массив в формате имя переменной => значение
     * @return string uri согласно паттерну заданного роута с учетом значений параметров. Если роут не найден - вернется значение /
     */
    public function generateRoute($route_name, $params = array())
    {
        $result = "/";
        if (array_key_exists($route_name, $this->config_array)) {
            $route_uri = $this->config_array[$route_name]["pattern"];
            foreach ($params as $param_name => $param_value) {
                $route_uri = str_replace("{" . $param_name . "}", $param_value, $route_uri);
            }
            $result = $route_uri;
        }
        return $result;
    }
}