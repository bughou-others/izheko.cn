<?php
require_once APP_ROOT . '/model/item.model.php';

class ItemListController
{
    static function index()
    {
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 60;
        list($items, $total_count) = Item::select($type, $page, $page_size);
        if(!$items) {
            header("X-Accel-Redirect: /static/404.htm");
            error_log('no items gotten');
            return;
        }
        $types = Item::types();
        ob_start();
        require_once APP_ROOT . '/view/item_list.view.php';
        $path = $_SERVER['REQUEST_DOCUMENT'];
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if(!is_dir($dir = dirname($full_path))) mkdir($dir, 0755, true);
        file_put_contents($full_path, ob_get_clean());
        header("X-Accel-Redirect: $path");
    }
}

ItemListController::index();

