<?php
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/model/item.model.php';

class ItemList extends ItemBase
{
    static function query($type, $word, $filter, $page, $page_size)
    {
        if($type && $type !== 'all') {
            $type_id = null;
            foreach(ItemBase::$types as $this_id => $tmp) {
                if ($tmp[1] === $type) {
                    $type_id = $this_id;
                    break;
                }
            }
            if($type_id === null) return;
            $condition = "and type_id=$type_id";
        }
        else $condition = '';

        if(strlen($word) > 0) {
            $word = DB::escape($word);
            $condition .= " and title like '%$word%'";
        } 

        $cond_9kuai9 = $condition . " and ref_price < 1000";
        $cond_20yuan = $condition . " and ref_price between 1000 and 2000";

        $data = array();
        self::filter($data, $filter, '9kuai9', $cond_9kuai9, $page, $page_size);
        self::filter($data, $filter, '20yuan', $cond_20yuan, $page, $page_size);
        self::filter($data, $filter, '',       $condition,   $page, $page_size);

        return $data;
    }

    static function filter(&$data, $filter, $target, $condition, $page, $page_size)
    {
        if($filter === $target) {
            self::select($data, $condition, $page, $page_size);
        } else {
            $data[$target . '_count'] = self::count($condition);
        }
    }

    static function count($condition)
    {
        $sql = 'select count(*) from items
            where title != "" and !(flags&' . ItemBase::FLAGS_MASK_ITEM_DELETED . ')' .
            $condition;
        return DB::get_value($sql);
    }

    static function select(&$data, $condition, $page, $page_size)
    {
        $offset = $page >= 1 ? ($page - 1) * $page_size : 0;
        $sql = "select SQL_CALC_FOUND_ROWS 
            num_iid,title,type_id,flags,ref_price,price,now_price,
            start_time,end_time,ref_tip, pic_url
            from items
            where title != '' and !(flags&" . ItemBase::FLAGS_MASK_ITEM_DELETED . ")
            $condition 
            order by ref_ordinal, id desc limit $offset, $page_size";
        $result = DB::query($sql);
        $instances = array();
        while($row = $result->fetch_assoc()) $instances[] = new Item($row);
        $data['items']       = &$instances;
        $data['total_count'] = DB::get_value('select found_rows()');
    }
}
