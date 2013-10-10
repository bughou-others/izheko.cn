<?php
require_once APP_ROOT . '/model/footprints.model.php';

class ItemController
{
    static function index()
    {
        $num_iid = $_GET['num_iid'];
        $sql_format = 'select 
            num_iid,title,type_id,flags,ref_price,price,now_price,
            start_time,end_time,ref_end_time,delist_time,ref_tip
            from %s
            where num_iid=' . $num_iid;
        $data = DB::get_row(sprintf($sql_format, 'items'));
        if(!$data) {
            $data = DB::get_row(sprintf($sql_format, 'items_history'));
        }

        if(!$data) {
            header('X-Accel-Redirect: /cache/404.html');
            error_log('no item: '. $num_iid);
            return;
        }
        $item = new Item($data);
        App::render('item', array('item' => $item));
    }
}

ItemController::index();

