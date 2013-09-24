<?php
require_once APP_ROOT . '/../common/model/category.model.php';

class TypeCmd
{
    static function start()
    {
        global $argv;
        $cmd  = isset($argv[1]) ? $argv[1] : null;


        if ($cmd === 'update_all_type_id')
            self::update_all_type_id();
        else
        {
            echo "unknow action: $cmd\n";
            return;
        }
    }

    static function update_all_type_id()
    { 
        $sql = "select num_iid,cid,title,type_id from items where cid > 0";
        $result = DB::query($sql);
        while(list($num_iid, $cid, $title, $old_type_id) = $result->fetch_row())
        {
            $type_id = Category::get_type_id($cid, $title);
            if($type_id != $old_type_id)
            {
                $sql = "update items set type_id=%s where num_iid=$num_iid";
                $affected = DB::update_affected_rows($sql, $type_id);
                echo "$num_iid $old_type_id => $type_id\n";
            }
        }
    }

}
TypeCmd::start();

