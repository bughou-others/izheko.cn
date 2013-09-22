<?php
require_once 'confirm_order.model.php';
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';
require_once APP_ROOT . '/../common/helper/json.helper.php';
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

        $item['postage_free'] =
            $item['freight_payer'] === 'seller' ||
            $item['post_fee']      === '0.00'   ||
            $item['express_fee']   === '0.00'   ||
            $item['ems_fee']       === '0.00';
        unset($item['freight_payer'], $item['post_fee'],
            $item['express_fee'], $item['ems_fee']);

        if(isset($item['price'])) $item['price'] = parse_price($item['price']);
        self::merge_promo_info($item, $num_iid);
        self::merge_real_price($item, $num_iid);

        return $item;
    }

    static function merge_real_price(&$item, $num_iid)
    {
        $now = time();
        if ($now < strtotime($item['list_time']) ||
            $now > strtotime($item['delist_time'])
        ) return;
        $cheapest = null;
        if(isset($item['skus']['sku']) && is_array($item['skus']['sku'])) {
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
        if($price && $price < $item['now_price']) {
            $item['now_price']  = $price;
            $item['price_type'] = '拍下改价';
        }
    }

    static function merge_promo_info(&$item, $num_iid)
    {
        $promo = self::get_promo_info($num_iid);
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

    static function get_promo_info($num_iid)
    {
        if (!$price_info = self::get_price_info($num_iid)) return;
        $promo = null;
        foreach ($price_info as $sku)
        {
            if (isset($sku['promotionList']) &&
                is_array($promo_list = $sku['promotionList'])
            )
            foreach($promo_list as $this_promo) {
                self::compare_promo($this_promo, $promo);
            }
        }
        if ($promo) {
            $promo['price_type'] = isset($promo['type']) &&
                ($promo['type'] === 'VIP价格' || $promo['type'] === '店铺vip')
                ? 'VIP价格' : '';
        }
        return $promo;
    }

    static function compare_promo($this_promo, &$promo)
    {
        if (isset($this_promo['price'])) 
        {
            if($this_promo['price'] < 0) {
                $this_promo['price'] = - parse_price(-$this_promo['price']);
                if(isset($promo['price'])) $this_promo['price'] += $promo['price'];
                $promo = $this_promo;
            }
            elseif (
                ($price = parse_price($this_promo['price'])) &&
                (is_null($promo) || $price < $promo['price'])
            )
            {
                $this_promo['price'] = $price;
                $promo = $this_promo;
            }
        }
    }

    static function get_price_info($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer = 'http://detail.tmall.com/item.htm?id=' . $num_iid;
        $url   = 'http://mdskip.taobao.com/core/initItemDetail.htm?queryMaybach=true&itemId=' . $num_iid;
        $response = $curl->get($url, $refer);

        $data = decode_json(iconv('GBK', 'UTF-8', $response->body));
        if ( isset($data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            ($price_info = $data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            is_array($price_info)
        ) return $price_info;
    }

    static function get_subtitle($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $url ='http://detail.tmall.com/item.htm?id=' . $num_iid . '&ali_trackid=2:mm_40339139_4152163_13484640';
        $response = $curl->get($url);
        $response->fix_charset();
        if ($t = $response->query('//div[@id="J_DetailMeta"]/div[@class="tb-property"]/div[@class="tb-wrap"]/div[@class="tb-detail-hd"]/p')->item(0))
            return trim($t->nodeValue);
    }

}


