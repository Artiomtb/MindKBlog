<?php

namespace Framework\Renderer;

use Framework\DI\Service;
use Framework\Response\Response;

class Renderer
{

    public static function render($view, $params, $with_layout = true)
    {
        $layout = "/home/asiliuk/Projects/MindKBlog/src/Blog/views/layout.html.php";

        $content = self::return_output($view, $params);

        $result = self::return_output($layout, array("content" => $content));
        return new Response($result);
    }

    private static function return_output($file, $params = array())
    {

        $route = array("_name" => Service::get("router")->getMatched()["name"]);

        extract($params);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}