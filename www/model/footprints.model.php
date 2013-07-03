<?php
require_once APP_ROOT . '/model/item.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';

class Footprints
{
    static function get($item_ids)
    {
        $sql = null;
        $item_ids = explode(',', $item_ids);
        foreach($item_ids as $id) {
            $sql .= ($sql === null ? '' : ' union all ') .
                'select id,title,type_id,flags,ref_price,price,promo_price,vip_price,
                promo_start,promo_end,list_time,delist_time,detail_url,click_url,pic_url
                from items where id = ' . $id;
        }
        $result = DB::query($sql);
        $data = array();
        while($row = $result->fetch_assoc()) {
            $item   = new Item($row);
            $data[] = array(
                'id'        => $row['id'],
                'pic_url'   => $item->pic_url(),
                'title'     => $item->title(),
                'jump_url'  => $item->jump_url(),
                'now_price' => format_price($item->now_price()),
            );
        }
        return $data;
    }
}
