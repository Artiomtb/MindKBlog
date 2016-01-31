<?php


namespace Framework\Router;

class Router
{

    private $config_array;
    private $request;

    /**
     * Конструктор, принимает на вход конфиг роутера
     * @param $config_array ассоциативный массив с роутами
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
     * Метод, перенаправляющий request на нужный контроллер
     * @param $request реквест
     */
    public function route($request)
    {
        $this->request = $request;
        $uri = $request->getUri();

        //отрежем параметры от uri, если они есть
        $getParamsPos = strpos($uri, "?");
        if ($getParamsPos != false) {
            $uri = substr($uri, 0, $getParamsPos);
        }

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

        //если подхордящий роут не найден
        if (is_null($matched_config)) {
            echo "Not found such route";
            //TODO имплементировать логику обработки не найденного маршрута
        } //роут найден, обработать
        else {
            echo "Founded !";
            print_r_mine($matched_config);
            $controller_name = $matched_config["controller"];
            $method_name = $matched_config["action"] . "Action";
            echo $controller_name . "->" . $method_name;
            $controller = new $controller_name();
            if (sizeof($uri_variables) == 0) {
//                $controller->$method_name();
                //TODO call methods without args
            } else {
                //TODO call methods with args
            }
        }
    }

    /**
     * Проверяет, соответствует ли uri паттерну
     * @param $parsedUri разложенный в массив uri
     * @param $parsedPattern разложенный в массив паттерн
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
     * @param $curPattern строка для проверки
     * @return bool true, если в строка - переменная в формате {name}
     */
    private function isVariable($curPattern)
    {
        preg_match("/^{[^{}]+}$/", $curPattern, $output_array);
        return sizeof($output_array) > 0;
    }

    /**
     * Метод проверяет, заканчивается ли строка символом "/"
     * @param $string строка для проверки
     * @return bool true, если строка заканчивается на "/"
     */
    private function isEndsWithSlash($string)
    {
        return substr($string, -1) === "/";
    }

    /**
     * Проверяет соблюдение _requirements конкретного конфига для uri
     * @param $matched_config конфиг роута
     * @param $uri_variables проверяемый uri
     * @return bool true, если условия соблюдаются
     */
    private function checkRequirements($matched_config, $uri_variables)
    {
        $result = true;
        $requirements = $matched_config["_requirements"];
        if (!is_null($requirements)) {
            foreach ($requirements as $reqName => $recValue) {

                //если есть ограничение по методу
                if ($reqName === "_method") {
                    if (strcmp($this->request->method, $recValue) != 0) {
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
}