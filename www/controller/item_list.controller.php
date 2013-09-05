<?php
require_once APP_ROOT . '/model/item_list.model.php';

class ItemListController
{
    static function index()
    {
        $type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $word   = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
        $page   = isset($_GET['page'])   ? intval($_GET['page']) : 1;
        $page_size = 6; // 1 * 2 * 3 * 2 * 5 *  2 * 3

        $data = ItemList::query($type, $word, $filter, $page, $page_size);
        if(strlen($word) > 0) {
            if(!is_array($data['items'])) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('search error');
                return;
            }
            unset($type);
            $page_url = "/search/$word/";
        }
        else
        {
            if(!is_array($data['items'])) {
                header('X-Accel-Redirect: /cache/404.html');
                error_log('no items gotten');
                return;
            }
            $page_url = $type && $type !== 'all' ? "/$type/" : '/';
        }
        App::render('item_list/item_list', compact('type', 'word', 'filter',  'page', 'page_size',
            'data', 'page_url'));
    }
}

ItemListController::index();

