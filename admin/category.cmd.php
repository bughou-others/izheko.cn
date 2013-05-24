<?php
require_once APP_ROOT . '/app/model/category.model.php';

class CategoryCmd
{
    static function start()
    {
        global $argv;
        $cmd  = isset($argv[1]) ? $argv[1] : null;

        if ($cmd === 'fetch_top_categories')
        {
            self::fetch_top_categories();
        }
        else
        {
            echo "unknow action: $type\n";
            return;
        }
    }

    static function fetch_top_categories()
    {
        $response = TaobaoApi::itemcats_children_get(0);
        if (!isset($response['itemcats_get_response']['item_cats']['item_cat']) ||
            !is_array($categories = $response['itemcats_get_response']['item_cats']['item_cat'])
        )
        {
            fputs(STDERR, "get top categories failed \n");
            return;
        }

        $count = count($categories);
        echo "get $count top categories \n";
        foreach ($categories as $category)
        {
            if (!Category::exist($category['cid']) && Category::save($category))
            {
                echo "saved new top category {$category['cid']} {$category['name']} \n";
            }
        }
    }
}
CategoryCmd::start();
