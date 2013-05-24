<?php
require_once APP_ROOT . '/app/model/item.php';

class ItemList
{
    static function index()
    {
        $items = Item::select(0, 60);
        require_once APP_ROOT . '/app/view/item_list.view.php';
    }
}

ItemList::index();

