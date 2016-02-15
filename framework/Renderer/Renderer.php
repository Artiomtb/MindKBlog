<?php

namespace Framework\Renderer;

class Renderer
{

    public static function render($file, $params)
    {
        extract($params);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}