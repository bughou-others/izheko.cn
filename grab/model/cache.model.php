<?php
require_once APP_ROOT . '/../common/helper/db.helper.php';
class Cache
{
    static function clear($type_ids)
    {
        if(!$type_ids) return;
        foreach($type_ids as $k => $v)
        {
            if(!preg_match('/^\d+$/', $v))
            {
                error_log("invlid type_id $v");
                unset($type_ids[$k]);
            }
        }
        $type_ids = implode(',', $type_ids);
        $sql = "select name, pinyin from types where id in ($type_ids)";
        $types = DB::get_map($sql);
        if(!$types) return;
        $types['全部'] = 'all';
        self::clear_cache($types);
    }

    static function clear_cache($types)
    {
        $base_dir = APP_ROOT . '/../www/public/cache';
        $prefixes = array('/type/', '/search/');
        
        foreach($types as $name => $pinyin)
        {
            foreach($prefixes as $prefix)
            {
                $cache = $prefix . $pinyin;
                $dir = $base_dir . $cache;
                if(is_dir($dir))
                {
                    $status = null;
                    system("rm -rf $dir", $status);
                    if($status === 0) $status = 'ok';
                    echo "clear cache $name $cache: $status\n";
                }
                else echo "clear cache $name $cache: no cache\n";
            }
        }
    }
}
