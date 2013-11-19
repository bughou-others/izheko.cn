<?php
require_once APP_ROOT . '/model/item_list.model.php';

class ItemListController
{
    static function index()
    {
        $type   = isset($_GET['type'])   ? trim($_GET['type'])   : '';
        $word   = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';
        $sort   = isset($_GET['sort'])   ? trim($_GET['sort']) : '';
        $page   = isset($_GET['page'])   ? intval($_GET['page']) : 1;
        $page_size = 180; // 1 * 2 * 3 * 2 * 5 *  2 * 3

        $data = ItemList::query($type, $word, $filter, $sort, $page, $page_size);
        if(strlen($word) > 0) {
            if(!is_array($data['items'])) {
                App::render404();
                error_log('search error');
                return;
            }
            unset($type);
            $type_url = "/search/$word/";
        }
        else
        {
            if(!is_array($data['items'])) {
                App::render404();
                error_log('no items gotten');
                return;
            }
            $type_url = $type && $type !== 'all' ? "/$type/" : '/';
        }
        $filter_url = $filter ? $type_url . $filter . '/' : $type_url;
        $sort_url   = $sort   ? $filter_url . $sort . '/' : $filter_url;

        App::render('item_list', compact('type', 'word', 'filter', 'sort', 'page', 'page_size',
            'data', 'type_url', 'filter_url', 'sort_url'));
    }
}

ItemListController::index();

