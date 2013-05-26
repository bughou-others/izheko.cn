<?php
require_once APP_ROOT . '/../common/db.php';

class CategoryManage
{
    static function db()
    {
        static $db;
        if (! $db) $db = DB::connect();
        return $db;
    }
    static function select($id = null)
    {
        $sql = "select * from categories ";
        if ($id) {
            $sql .= " where id = $id" ;
            return self::db()->query($sql)->fetch_assoc();
        }
        return self::db()->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    static function update($id, $type_id)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id)) ||
            !(preg_match('/^\d+$/', $type_id)) ||
            $type_id <= 0
        ) return;

        $sql = "update categories set type_id='$type_id', update_time=now()
            where id = $id and type_id != '$type_id'";
        return self::db()->query($sql);
    }
}
