<?php
require_once 'item_promo.model.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';
require_once APP_ROOT . '/../common/model/category.model.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';

class TaobaoItem
{
    static function get_item_info($num_iid)
    {
        if($result = TaobaoApi::item_get($num_iid))
        {
            if(isset($result['item_get_response']['item']))
                $item = $result['item_get_response']['item'];
            elseif(
                isset($result['error_response']['sub_msg']) &&
                ( ($sub_msg = $result['error_response']['sub_msg']) === '该商品已被删除'
                || $sub_msg === '未登录用户不能获取小二下架或删除的商品')
            ) return 'deleted';
            else return;
        }
        else return;

        $item['type_id'] = Category::get_type_id($item['cid'], $item['title']);

        if(isset($item['price'])) $item['price'] = parse_price($item['price']);
        self::merge_promo_info($item, $num_iid);
        #self::merge_real_price($item, $num_iid);

        self::set_flags($item);
        return $item;
    }

    static function merge_promo_info(&$item, $num_iid)
    {
        $promo = ItemPromo::get_promo_info($num_iid);
        if(is_array($promo) && $promo['price'] < $item['price']) {
            $item['now_price']  = $promo['price'];
            if($promo['price'] < 0) $item['now_price'] += $item['price'];

            $item['start_time'] = isset($promo['startTime']) && is_int($promo['startTime']) &&
                ($start_time = $promo['startTime'] / 1000) > strtotime($item['list_time']) ?
                strftime('%F %T', $start_time) : $item['list_time'];
            $item['end_time']   = isset($promo['endTime']) && is_int($promo['endTime']) && 
                ($end_time = $promo['endTime'] / 1000)     < strtotime($item['delist_time']) ?
                strftime('%F %T', $end_time)   : $item['delist_time'];
            $item['price_type'] = $promo['price_type'];
        } else {
            $item['now_price']  = $item['price'];
            $item['start_time'] = $item['list_time'];
            $item['end_time']   = $item['delist_time'];
            $item['price_type'] = null;
        }
    }

    static function set_flags(&$item)
    {
        $item['flags'] = self::mask_bits(0, ItemBase::FLAGS_MASK_POSTAGE_FREE,
            $item['freight_payer'] === 'seller' ||
            $item['post_fee']      === '0.00'   ||
            $item['express_fee']   === '0.00'   ||
            $item['ems_fee']       === '0.00'
        );
        $item['flags'] = self::mask_bits($item['flags'], ItemBase::FLAGS_MASK_VIP_PRICE,
            $item['price_type'] === 'VIP价格'
        );
        $item['flags'] = self::mask_bits($item['flags'], ItemBase::FLAGS_MASK_CHANGE_PRICE,
            $item['price_type'] === '拍下改价'
        );
        $item['flags'] = self::mask_bits($item['flags'], ItemBase::FLAGS_MASK_TMALL,
            $item['auction_point'] > 0
        );
        unset($item['freight_payer'], $item['post_fee'], $item['express_fee'], $item['ems_fee']);
    }

    static function mask_bits($bits, $mask, $bool)
    {
        return $bool ? ($bits | $mask) : ($bits & ~$mask);
    }

    static function merge_real_price(&$item, $num_iid)
    {
        $now = time();
        if ($now < strtotime($item['list_time']) ||
            $now > strtotime($item['delist_time'])
        ) return;
        $cheapest = null;
        if (isset($item['skus']['sku']) && is_array($item['skus']['sku'])) {
            $skus = $item['skus']['sku'];
            foreach($skus as $sku) {
                if ($sku['quantity'] > 0 && (
                  $cheapest === null ||  $sku['price'] < $cheapest['price']
                )) $cheapest = $sku;
            }
        }
        unset($item['skus']);
        $price = ConfirmOrder::get_price($num_iid,
            isset($cheapest['sku_id']) ? $cheapest['sku_id'] : null,
            $item['auction_point'] > 0
        );
        if ($price && $price < $item['now_price']) {
            $item['now_price']  = $price;
            $item['price_type'] = '拍下改价';
        }
    }

}


