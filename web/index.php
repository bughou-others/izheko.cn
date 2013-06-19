<?php
# vim: ft=php
define('APP_ROOT', dirname(__FILE__));

class App
{
    static function run_controller()
    {
        if (!isset($_SERVER['DOCUMENT_URI']) || !($target = $_SERVER['DOCUMENT_URI']))
        {
            #header("HTTP/1.0 404 Not Found");
            header("X-Accel-Redirect: /cache/404.html");
            error_log('no DOCUMENT_URI given');
            return;
        }
            
        $target = APP_ROOT . '/controller' . $target;
        if (file_exists($target)) require_once $target;
        else {
            header("X-Accel-Redirect: /cache/404.html");
            error_log('controller not exist: ' . $target);
        }
    }

    const cache = true;

    static function render($target_view, $data = null)
    {
        if(self::cache) {
            ob_start();
            self::real_render($target_view, $data);
            $path = $_SERVER['REQUEST_DOCUMENT'];
            $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
            if(!is_dir($dir = dirname($full_path))) mkdir($dir, 0755, true);
            file_put_contents($full_path, ob_get_clean());
            header("X-Accel-Redirect: $path");
        }
        else self::real_render($target_view, $data);
    }
    static function real_render($target_view, $data)
    {
        if($data) extract($data);
        require_once APP_ROOT . '/view/layout.view.php';
    }
}
App::run_controller();


