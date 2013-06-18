<?php
require_once APP_ROOT . '/model/item.model.php';

class ItemListController
{
    static function index()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 60;
        if (isset($_GET['search']))
        {
            $word = $_GET['search'];
            list($items, $total_count) = Item::search($word, $page, $page_size);
            if(!is_array($items)) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('search error');
                return;
            }
            $page_url = "/search/$word/";
        }
        else
        {
            $type = isset($_GET['type']) ? $_GET['type'] : '';
            list($items, $total_count) = Item::query($type, $page, $page_size);
            if(!$items) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('no items gotten');
                return;
            }
            $page_url = $type ? "/$type/" : '/';
        }
        #ob_start();
        $target_view = 'item_list';
        require_once APP_ROOT . '/view/layout.view.php';
        /*
        $path = $_SERVER['REQUEST_DOCUMENT'];
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if(!is_dir($dir = dirname($full_path))) mkdir($dir, 0755, true);
        file_put_contents($full_path, ob_get_clean());
        header("X-Accel-Redirect: $path");
         */
    }
}

ItemListController::index();

