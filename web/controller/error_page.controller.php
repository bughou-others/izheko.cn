<?php

class ErrorPage
{
    static function gen_404()
    {
        ob_start();
        require_once APP_ROOT . '/view/layout.view.php';
        $path = $_SERVER['REQUEST_DOCUMENT'];
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if(!is_dir($dir = dirname($full_path))) mkdir($dir, 0755, true);
        file_put_contents($full_path, ob_get_clean());
        header("X-Accel-Redirect: $path");
    }
}

ErrorPage::gen_404();

