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
                'select id,title,type_id,flags,ref_price,price,now_price,
                start_time,end_time,delist_time,detail_url,click_url,pic_url
                from ' . ($history ? 'items' : 'items_history') . ' where id =  ' . $id;
        }
        $result = DB::query($sql);
        while($row = $result->fetch_assoc()) {
            $item   = new Item($row);
            $id = $row['id'];
            if(!$history && ($index = array_search($id, $item_ids, true)) !== false)
                unset($item_ids[$index]);
            $data[] = array(
                'id'        => $id,
                'pic_url'   => $item->pic_url(),
                'title'     => $item->title(),
                'jump_url'  => $item->jump_url(),
                'now_price' => format_price($item->get('now_price')),
            );
        }
        return $data;
    }
}
