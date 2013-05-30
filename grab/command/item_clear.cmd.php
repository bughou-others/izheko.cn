<?php
require_once APP_ROOT . '/app/model/item.php';

class ItemClear
{
    static function start()
    {
        $instance = new self;
        $instance->clear();
    }

    function clear()
    {
        #delisted
        #too expensive than ref price
        $sql = 'select num_iid from items where title = "" ';
        $result = DB::query($sql);
        echo "{$result->num_rows} item to update\n";
        while(list($num_iid) = $result->fetch_row())
        {
            if ($info = TaobaoItem::get_item_info($num_iid))
                $this->update_item($num_iid, $info);
        }
    }
}
ItemClear::start();


