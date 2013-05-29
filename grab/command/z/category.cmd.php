<?php
require_once APP_ROOT . '/../common/model/category.model.php';

class CategoryCmd
{
    static function start()
    {
        global $argv;
        $cmd  = isset($argv[1]) ? $argv[1] : null;
        $cid  = isset($argv[2]) ? $argv[2] : null;

        if(!preg_match('/^\d+$/', $cid))
        {
            echo "a number cid must be given.\n";
            return;
        }

        if ($cmd === 'get_type')
            self::get_type($cid);
        elseif ($cmd === 'get_type_id')
            var_dump(Category::get_type_id($cid));
        elseif ($cmd === 'fetch_children')
            self::fetch_children($cid);
        elseif ($cmd === 'fetch_children_recursively')
            self::fetch_children($cid, true);
        else
        {
            echo "unknow action: $type\n";
            return;
        }
    }

    static function get_type($cid)
    { 
        while($cid) {
            if(!$category = Category::get($cid))
            {
                echo "get category $cid failed\n";
                var_dump($category);
                break;
            }
            if(isset($category['type_id']) && $category['type_id'] > 0)
            {
                break;
            }
            $cid = $category['parent_cid'];
        }
        var_dump($category);
    }

    static function fetch_children($cid, $recursive = false)
    {
        $categories = Category::get_children_from_api($cid);
        if(!is_array($categories))
        {
            fputs(STDERR, "get children categories of $cid failed \n");
            return;
        }
        $count = count($categories);
        echo "$cid: got $count children categories\n";
        foreach ($categories as $category)
        {
            if(Category::exist($category['cid']))
                echo "exists {$category['cid']} {$category['name']} \n";
            elseif(Category::save($category))
                echo "saved {$category['cid']} {$category['name']} \n";
            else
                echo "failed saving {$category['cid']} {$category['name']} \n";
        }

        if(!$recursive) return;
        foreach ($categories as $category)
        {
            if($category['is_parent'])
                self::fetch_children($category['cid'], true);
        }
    }
}
CategoryCmd::start();

