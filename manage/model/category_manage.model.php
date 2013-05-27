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
    static function select($page, $limit)
    {
        $offset = $page >= 1 ? ($page - 1) * $limit : 0;
        $sql = "select SQL_CALC_FOUND_ROWS * from categories limit $offset, $limit";
        $data = self::db()->query($sql)->fetch_all(MYSQLI_ASSOC);
        $row  = self::db()->query('select found_rows()')->fetch_row();
        return array($data, $row[0]);
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
