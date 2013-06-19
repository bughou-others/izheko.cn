<?php
require_once APP_ROOT . '/model/item.model.php';

class ItemListController
{
    static function index()
    {
        $type = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $word = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page_size = 60;

        list($items, $total_count) = Item::query($type, $word, $page, $page_size);
        if(strlen($word) > 0) {
            if(!is_array($items)) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('search error');
                return;
            }
            if($type === '')$type = 'all';
            $page_url = "/search/$type/$word/";
        }
        else
        {
            if(!$items) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('no items gotten');
                return;
            }
            $page_url = $type && $type !== 'all' ? "/$type/" : '/';
        }
        App::render('item_list', compact('type', 'word', 'page', 'page_size',
            'items', 'total_count', 'page_url'));
    }
}

ItemListController::index();

