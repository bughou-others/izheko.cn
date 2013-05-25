<?php
require_once APP_ROOT . '/model/item.model.php';

class ItemListController
{
    static function index()
    {
        $items = Item::select(0, 60);
        require_once APP_ROOT . '/view/item_list.view.php';
    }
}

ItemListController::index();

