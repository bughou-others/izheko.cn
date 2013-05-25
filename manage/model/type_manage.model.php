<?php
require_once APP_ROOT . '/../common/db.php';

class TypeManage
{
    static function db()
    {
        static $db;
        if (! $db) $db = DB::connect();
        return $db;
    }
    static function select($id = null)
    {
        $sql = "select id, name, create_time, update_time from types ";
        if ($id) {
            $sql .= " where id = $id" ;
            return self::db()->query($sql)->fetch_assoc();
        }
        return self::db()->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    static function update($id, $name)
    {
        $name = self::db()->escape_string($name);
        $sql = "update ignore types set name='$name', update_time=now() where id = $id ";
        return self::db()->query($sql);
    }

    static function insert($name)
    {
        $name = self::db()->escape_string($name);
        $sql  = "insert ignore into types (name, create_time) values ('$name', now())";
        return self::db()->query($sql);
    }

    static function delete($id)
    {
        $sql = "delete from types where id = $id";
        return self::db()->query($sql);
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
