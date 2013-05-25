#!/usr/bin/php
<?php
# vim: ft=php

define('APP_ROOT', dirname(__FILE__));

class App
{
    static function run_controller()
    {
        if (!isset($_SERVER['DOCUMENT_URI']) || !($target = $_SERVER['DOCUMENT_URI']))
        {
            header("HTTP/1.0 404 Not Found");
            return;
        }
            
        if (substr($target, -3) == '.do')
            $target = substr($target, 0, -3);
        else
        {
            if (preg_match('//', $target))
                $target = '';
            elseif (preg_match('//', $target))
                $target = '';
            else $target = '';
        }
        $target = APP_ROOT . "/controller/$target.controller.php";
        if (file_exists($target)) require_once $target;
        else header("HTTP/1.0 404 Not Found");
    }
}

App::run_controller();


