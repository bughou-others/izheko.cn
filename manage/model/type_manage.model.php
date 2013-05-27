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

    static function get_types()
    {
        $sql = "select id, name from types ";
        $result = self::db()->query($sql);
        $types = array();
        while($row = $result->fetch_assoc())
            $types[$row['id']] = $row['name'];
        return $types;
    }

    static function update($id, $name)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id)) ||
            !($name = trim($name))
        ) return;

        $name = self::db()->escape_string($name);
        $sql = "update ignore types set name='$name', update_time=now()
            where id = $id";
        if (self::db()->query($sql))
            return self::db()->affected_rows;
        else return false;
    }

    static function insert($name)
    {
        if (! $name = trim($name)) return;
        $name = self::db()->escape_string($name);
        $sql  = "insert ignore into types (name, create_time) values ('$name', now())";
        if (self::db()->query($sql))
            return self::db()->insert_id;
        else return false;
    }

    static function delete($id)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id))
        ) return;
        $sql = "delete from types where id = $id";
        if (self::db()->query($sql))
            return self::db()->affected_rows;
        else return false;
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
