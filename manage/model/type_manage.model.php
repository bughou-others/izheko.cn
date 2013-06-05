<?php
require_once APP_ROOT . '/../common/db.php';

class TypeManage
{
    static function select($id = null)
    {
        $sql = "select id, name, pinyin, create_time, update_time from types ";
        if ($id) {
            $sql .= " where id = $id" ;
            return DB::get_row($sql);
        }
        return DB::get_rows($sql);
    }

    static function get_types()
    {
        $sql = "select id, name from types ";
        return DB::get_map($sql);
    }

    static function update($id, $name, $pinyin)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id)) ||
            !($name = trim($name)) ||
            !($pinyin = trim($pinyin)) ||
            !(preg_match('/^[a-z]+$/', $pinyin))
        ) return;

        $sql = "update ignore types set %s, update_time=now()
            where id = $id ";
        return DB::update_affected_rows($sql, array('name' => $name, 'pinyin' => $pinyin));
    }

    static function insert($name)
    {
        if (! $name = trim($name)) return;
        $name = DB::escape($name);
        $sql  = "insert ignore into types (name, create_time) values ('$name', now())";
        return DB::insert_id($sql);
    }

    static function delete($id)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id))
        ) return;
        $sql = "delete from types where id = $id";
        return DB::affected_rows($sql);
    }

    function __construct($data)
    {
        $this->data = $data;
    }
    
    function get($name)
    {
        if (isset($this->data[$name])) return $this->data[$name];
    }
}
