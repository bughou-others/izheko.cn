<?php
require_once APP_ROOT . '/../common/helper/db.helper.php';

class CategoryManage
{
    static function select($condition, $page, $limit)
    {
        if (isset($condition['traceup']) &&
            ($traceup = $condition['traceup']) &&
            preg_match('/^\d+$/', $traceup)
        ) return self::traceup($traceup);

        if(isset($condition['parent']) &&
            ($parent = $condition['parent']) &&
            preg_match('/^\d+$/', $parent)
        )
        {
            require_once APP_ROOT . '/../common/model/category.model.php';
            Category::fetch_children($parent);
            $where = " where parent_cid=$parent";
        }
        else $where = null;
            
        $offset = $page >= 1 ? ($page - 1) * $limit : 0;
        $sql = "select SQL_CALC_FOUND_ROWS * from categories $where limit $offset, $limit";
        list($rows, $field) = DB::get_rows_and_field($sql, 'parent_cid');
        $found_rows = DB::get_value('select found_rows()');

        $data = self::add_parent_name($rows, $field);
        return array($data, $found_rows);
    }

    static function traceup($cid)
    {
        require_once APP_ROOT . '/../common/model/category.model.php';
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

    static function add_parent_name($data, $cids)
    {
        if(empty($cids)) return $data;
        $cids = implode(',', array_unique($cids));
        $sql = "select cid, name from categories where cid in ($cids)";
        $names = DB::get_map($sql);
        foreach($data as &$category)
        {
            $cid = $category['parent_cid'];
            if(isset($names[$cid])) $category['parent_name'] = $names[$cid];
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

        $sql = "update categories set type_id=$type_id, update_time=now()
            where id = $id and type_id != $type_id";
        if($a = DB::affected_rows($sql))
        {
            $sql = "select update_time from categories where id = $id";
            return DB::get_value($sql);
        }
        else return $a;
    }
}
