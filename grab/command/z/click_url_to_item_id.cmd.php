<?php
require_once APP_ROOT . '/model/click_url_to_item_id.model.php';

class ClickUrlToItemIdCmd
{
    static function start()
    {
        global $argv;
        if(!$click_url = isset($argv[1]) ? $argv[1] : null) 
            return;
        $item_id = ClickUrlToItemId::fetch($click_url);
        var_dump($item_id);
    }
}

ClickUrlToItemIdCmd::start();

