<?php
require_once APP_ROOT . '/model/taobao_item/taobao_item.model.php';

class TaobaoItemCmd
{
    static function start()
    {
        global $argv;
        $type    = @$argv[1];
        $num_iid = @$argv[2];
        if ($type ===  'promo_info') {
            $result = TaobaoItem::get_promo_info($num_iid);
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

