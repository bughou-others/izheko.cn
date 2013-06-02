<?php
require_once APP_ROOT . '/../common/model/taobao_api.model.php';

class TaobaoApiCmd
{
    static function start()
    {
        global $argv;
        $type    = @$argv[1];
        $num_iid = @$argv[2];
        if ($type ===  'item')
            $response = TaobaoApi::item_get($num_iid);
        elseif ($type ===  'promo')
            $response = TaobaoApi::ump_promotion_get($num_iid);
        elseif ($type ===  'click_url')
            $response = TaobaoApi::taobaoke_items_detail_get($num_iid);
        elseif ($type ===  'coupon')
        {
            $response = TaobaoApi::item_get($num_iid);
            if($info = @$response['item_get_response']['item'])
            {
                $title = preg_replace('/^【[^】]*】/', '', $info['title']);
                $response = TaobaoApi::taobaoke_items_coupon_get($title, $info['cid']);
            }
        }
        elseif ($type ===  'cats')
        {
            $cids = @$argv[2];
            $response = TaobaoApi::itemcats_get($cids);
        }
        elseif ($type ===  'children_cats')
        {
            $cids = @$argv[2];
            $response = TaobaoApi::itemcats_children_get($cids);
        }
        else
        {
            echo "unknow action: $type\n";
            return;
        }
        var_dump($response);
    }
}
TaobaoApiCmd::start();
