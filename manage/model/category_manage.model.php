<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/../common/model/category.model.php';

class CategoryManage
{
    static function db()
    {
        static $db;
        if (! $db) $db = DB::connect();
        return $db;
    }
    static function select($condition, $page, $limit)
    {
        if (isset($condition['traceup']) &&
            ($traceup = $condition['traceup']) &&
            preg_match('/^\d+$/', $traceup)
        ) return self::traceup($traceup);

        $where = (isset($condition['parent']) &&
            ($parent = $condition['parent']) &&
            preg_match('/^\d+$/', $parent)
        ) ? " where parent_cid=$parent" : null;
            
        $offset = $page >= 1 ? ($page - 1) * $limit : 0;
        $sql = "select SQL_CALC_FOUND_ROWS * from categories $where limit $offset, $limit";
        $result = self::db()->query($sql);
        $row = self::db()->query('select found_rows()')->fetch_row();
        $found_rows = $row[0];

        $data = self::add_parent_name($result);
        return array($data, $found_rows);
    }

    static function traceup($cid)
    {
        $data = array();
        while($cid && ($category = Category::get($cid)))
        {
            if($count = count($data))
                $data[$count - 1]['parent_name'] = $category['name'];
            $data[] = $category;
            $cid = $category['parent_cid'];
        }
        return array($data, count($data));
    }

    static function add_parent_name($result)
    {
        $data = array();
        $parent_cids = array();
        while($row = $result->fetch_assoc())
        {
            $data[] = $row;
            $parent_cids[] = $row['parent_cid'];
        }
        $names = self::get_names($parent_cids);
        foreach($data as &$category)
        {
            $cid = $category['parent_cid'];
            if(isset($names[$cid])) $category['parent_name'] = $names[$cid];
        }
        return $data;
    }

    static function get_names($cids)
    {
        if(empty($cids)) return array();
        $cids = implode(',', array_unique($cids));
        $sql = "select cid, name from categories where cid in ($cids)";
        $result = self::db()->query($sql);
        $data = array();
        while($row = $result->fetch_assoc())
        {
            $data[$row['cid']] = $row['name'];
        }
        return $data;
    }

    static function update($id, $type_id)
    {
        if (!($id = trim($id)) ||
            !(preg_match('/^\d+$/', $id)) ||
            !(preg_match('/^\d+$/', $type_id)) ||
            $type_id <= 0
        ) return;

        $sql = "update categories set type_id='$type_id', update_time=now()
            where id = $id";
        return self::db()->query($sql);
    }
}
