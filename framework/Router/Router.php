<?php


namespace Framework\Router;

class Router
{

    private $config_array;

    public function __construct($config_array)
    {
        foreach ($config_array as $name => $config) {
            if (!$this->isEndsWithSlash($config["pattern"])) {
                $config_array[$name]["pattern"] = $config["pattern"] . "/";
            }
        }
        $this->config_array = $config_array;
    }

    public function route($request)
    {
        $uri = $request->getUri();

        $getParamsPos = strpos($uri, "?");
        if ($getParamsPos != false) {
            $uri = substr($uri, 0, $getParamsPos);
        }

        if (!$this->isEndsWithSlash($uri)) {
            $uri = $uri . "/";
        };

        $parsedUri = explode("/", $uri);

        $matched_config = null;
        $uri_variables = null;
        foreach ($this->config_array as $name => $config) {
            $pattern = $config["pattern"];
            $parsedPattern = explode("/", $pattern);
            $uri_variables = $this->checkAndGetVariables($parsedUri, $parsedPattern);
            if (!is_null($uri_variables) && $this->checkRequirements($config, $uri_variables)) {
                $matched_config = $config;
                break;
            }
        }
        if (is_null($matched_config)) {
            echo "Not found such route";
        } else {
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

    private function checkAndGetVariables($parsedUri, $parsedPattern)
    {
        $variables = array();
        $patternSize = sizeof($parsedPattern);
        if (sizeof($parsedUri) === $patternSize) {
            for ($i = 1; $i < $patternSize - 1; $i++) {
                $curPattern = $parsedPattern[$i];
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

    private function isVariable($curPattern)
    {
        preg_match("/^{[^{}]+}$/", $curPattern, $output_array);
        return sizeof($output_array) > 0;
    }

    private function isEndsWithSlash($string)
    {
        return substr($string, -1) === "/";
    }

    private function checkRequirements($matched_config, $uri_variables)
    {
        $result = true;
        $requirements = $matched_config["_requirements"];
        if (!is_null($requirements)) {
            foreach ($requirements as $reqName => $recValue) {
                if ($reqName === "method") {
                    echo "Checking method";
                } elseif (!is_null($uri_variables[$reqName])) {
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