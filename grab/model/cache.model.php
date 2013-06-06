<?php
require_once APP_ROOT . '/../common/db.php';
class Cache
{
    static function clear($type_ids)
    {
        if(!$type_ids) return;
        $type_ids = implode(',', $type_ids);
        $sql = "select name, pinyin from types where id in ($type_ids)";
        $types = DB::get_map($sql);
        if(!$types) return;
        $types['全部'] = 'all';
        $cache_base_dir = APP_ROOT . '/../web/public/cache/';
        foreach($types as $name => $pinyin)
        {
            $cache_type_dir = $cache_base_dir . $pinyin;
            if(is_dir($cache_type_dir))
            {
                $status = null;
                system("rm -rf $cache_type_dir", $status);
                echo "clear cache $name $pinyin: $status\n";
            }
            else echo "clear cache $name $pinyin: no cache\n";
        }
    }
}
