<?php
require_once APP_ROOT . '/model/item.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';

class Footprints
{
    static function get($item_ids)
    {
        $item_ids = explode(',', $item_ids);
        $data = array();
        self::get_items($data, $item_ids);
        if(!empty($item_ids)) self::get_items($data, $item_ids, true);
        return $data;
    }

    static function get_items(&$data, &$item_ids, $history = false)
    {
        $sql = null;
        foreach($item_ids as $id) {
            $sql .= ($sql === null ? '' : ' union all ') .
                'select num_iid,title,type_id,flags,ref_price,price,now_price,
                start_time,end_time,pic_url
                from ' . ($history ? 'items' : 'items_history') . ' where num_iid =  ' . $id;
        }
        $result = DB::query($sql);
        while($row = $result->fetch_assoc()) {
            $item   = new Item($row);
            $num_iid = $row['num_iid'];
            if(!$history && ($index = array_search($num_iid, $item_ids, true)) !== false)
                unset($item_ids[$index]);
            $data[] = array(
                'num_iid'   => $num_iid,
                'url'       => $item->url(),
                'pic_url'   => $item->pic_url(),
                'title'     => $item->title(),
                'now_price' => format_price($item->get('ref_price')),
            );
        }
        return $data;
    }
}
