<?php
require_once APP_ROOT . '/model/taobao_item.model.php';

class TaobaoItemCmd
{
    static function start()
    {
        global $argv;
        $type    = @$argv[1];
        $num_iid = @$argv[2];
        if ($type ===  'promo_info') {
            $item   = TaobaoItem::get_item_info($num_iid);
            $result = TaobaoItem::get_promo_info($num_iid, $item['auction_point'] > 0, $item['title']);
        }
        elseif ($type ===  'subtitle')
            $result = TaobaoItem::get_subtitle($num_iid);
        elseif ($type ===  'price_info')
            $result = TaobaoItem::get_price_info($num_iid);
        elseif ($type ===  'item_info')
            $result = TaobaoItem::get_item_info($num_iid);
        else
        {
            echo "unknow action: $type\n";
            return;
        }
        var_dump($result);
    }
}
TaobaoItemCmd::start();

