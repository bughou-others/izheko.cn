<?php
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/model/item.model.php';

class ItemList extends ItemBase
{
    static function time_cond($cond)
    {
        return " and list_time $cond and (
            promo_start is null or promo_price > vip_price or promo_start $cond
        )";
    }

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

        $now      = strftime('%F %T');
        $today    = strftime('%F %T', strtotime('today'));
        $tomorrow = strftime('%F %T', strtotime('tomorrow'));
        $new_cond      = $condition . self::time_cond("between '$today' and '$tomorrow'");
        $coming_cond   = $condition . self::time_cond("between '$now'   and '$tomorrow'");
        $tomorrow_cond = $condition . self::time_cond(">= '$tomorrow'");
        $default_cond  = $condition . (strlen($word) > 0 ? '' : self::time_cond("< '$tomorrow'"));

        $data = array();
        self::filter($data, $filter, 'new',      $new_cond,      $page, $page_size);
        self::filter($data, $filter, 'coming',   $coming_cond,   $page, $page_size);
        self::filter($data, $filter, 'tomorrow', $tomorrow_cond, $page, $page_size);
        self::filter($data, $filter, null,       $default_cond,  $page, $page_size);

        return $data;
    }

    static function filter(&$data, $filter, $target, $condition, $page, $page_size)
    {
        if($filter === $target) {
            self::select($data, $condition, $page, $page_size);
        } elseif ($target !== null) {
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
            id, title,type_id,flags,ref_price,price,promo_price,vip_price,
            promo_start,promo_end,list_time,delist_time,detail_url,click_url,pic_url
            from items
            where title != '' and !(flags&" . ItemBase::FLAGS_MASK_ITEM_DELETED . ")
            $condition 
            order by id desc limit $offset, $page_size";
        $result = DB::query($sql);
        $instances = array();
        while($row = $result->fetch_assoc()) $instances[] = new Item($row);
        $data['items']       = &$instances;
        $data['total_count'] = DB::get_value('select found_rows()');
    }
}