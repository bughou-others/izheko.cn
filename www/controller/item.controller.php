<?php
require_once APP_ROOT . '/model/footprints.model.php';

class ItemController
{
    static function index()
    {
        $num_iid = $_GET['num_iid'];
        $sql = 'select 
            id, num_iid, title,type_id,flags,ref_price,price,now_price,
            start_time,end_time,delist_time,click_url,pic_url
            from items
            where num_iid=' . $num_iid;
        $data = DB::get_row($sql);
        $item = $data ? new Item($data) : null;
        App::render('item_list/item', array('item' => $item));
    }
}

ItemController::index();

