<?php
class Type
{
    static function all()
    {
        static $types;
        if(isset($types)) return $types;
        $sql = "select id, name, pinyin from types";
        $result = DB::query($sql);
        $types = array();
        while(list($id, $name, $pinyin) = $result->fetch_row())
        {
            $types[$id] = array($name, $pinyin);
        }
        return $types;
    }
}
