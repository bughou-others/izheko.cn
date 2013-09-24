<?php
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/model/item.model.php';

class ItemList extends ItemBase
{
    static function query($type, $word, $filter, $page, $page_size)
    {
        if($type && $type !== 'all') {
            $type_id = DB::get_value("select id from types where pinyin = '$type'");
            if(!$type_id) return;
            $condition = "and type_id=$type_id";
        }
        else $condition = '';

        if(strlen($word) > 0) {
            $word = DB::escape($word);
            $condition .= " and title like '%$word%'";
        } 
        $today_time   = strtotime('today');
        $now          = strftime('%F %T');
        $today        = strftime('%F %T', $today_time);
        $today_end    = strftime('%F %T', $today_time + 86399);
        $tomorrow     = strftime('%F %T', $today_time + 86400);
        $tomorrow_end = strftime('%F %T', $today_time + 86400 + 86399);
        $new_cond      = $condition . " and start_time between '$today' and '$today_end'";
        $coming_cond   = $condition . " and start_time between '$now'   and '$today_end'";
        $tomorrow_cond = $condition . " and start_time between '$tomorrow' and '$tomorrow_end'";
        $default_cond  = $condition . (strlen($word) > 0 ? '' : " and start_time < '$now'");

        $data = array();
        self::filter($data, $filter, 'new',      $new_cond,      $page, $page_size);
        self::filter($data, $filter, 'coming',   $coming_cond,   $page, $page_size);
        self::filter($data, $filter, 'tomorrow', $tomorrow_cond, $page, $page_size);
        self::filter($data, $filter, '',         $default_cond,  $page, $page_size);

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
            start_time,end_time,ref_end_time,delist_time,ref_tip
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
