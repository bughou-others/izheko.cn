<?php
require_once APP_ROOT . '/model/click_url.model.php';

class ClickUrlCmd
{
    static function start()
    {
        global $argv;
        if(!$click_url = isset($argv[1]) ? $argv[1] : null) 
            return;
        $item_id = ClickUrl::to_item_id($click_url);
        var_dump($item_id);
    }
}

ClickUrlCmd::start();

