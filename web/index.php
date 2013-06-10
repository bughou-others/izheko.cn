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
            header("X-Accel-Redirect: /static/404.htm");
            error_log('no DOCUMENT_URI given');
            return;
        }
            
        $target = APP_ROOT . '/controller' . $target;
        if (file_exists($target)) require_once $target;
        else {
            header("X-Accel-Redirect: /static/404.htm");
            error_log('controller not exist: ' . $target);
        }
    }
}
App::run_controller();


