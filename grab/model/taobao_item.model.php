<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';
require_once APP_ROOT . '/../common/helper/json.helper.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/../common/helper/number.helper.php';

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

        if(isset($item['price']))
            $item['price'] = parse_price($item['price']);
        if(is_array($promo = self::get_promo_info($num_iid)) &&
            $promo['price'] < $item['price']
        ) {
            $item['now_price']  = $promo['price'];
            $item['start_time'] = is_int($promo['startTime']) &&
                ($start_time = $promo['startTime'] / 1000) > strtotime($item['list_time']) ?
                strftime('%F %T', $start_time) : $item['list_time'];
            $item['end_time']   = is_int($promo['endTime']) && 
                ($end_time = $promo['endTime'] / 1000)     < strtotime($item['delist_time']) ? 
                strftime('%F %T', $end_time)   : $item['delist_time'];
            $item['vip_price']  = $promo['vip_price'];
        }
        else {
            $item['now_price']  = $item['price'];
            $item['start_time'] = $item['list_time'];
            $item['end_time']   = $item['delist_time'];
            $item['vip_price']  = false;
        }

        return $item;
    }

    static function get_promo_info($num_iid)
    {
        if(!$price_info = self::get_price_info($num_iid)) return;
        $promo = null;
        foreach ($price_info as $sku)
        {
            if (isset($sku['promotionList']) &&
                is_array($promo_list = $sku['promotionList'])
            )
            foreach($promo_list as $this_promo)
            {
                 parse_change_price($this_promo);
                if (is_array($this_promo) && isset($this_promo['price']) &&
                    ($price = parse_price($this_promo['price'])) &&
                    (is_null($promo) || $price < $promo['price'])
                ){
                    $this_promo['price'] = $price;
                    $promo = $this_promo;
                }
            }
        }
        if($promo) {
            $promo['vip_price'] = isset($promo['type']) &&
                ($promo['type'] === 'VIP价格' || $promo['type'] === '店铺vip');
        }
        return $promo;
    }

    static function parse_change_price(&$promo)
    {
        $type = $promo['type'];
        if (preg_match(
            '/^拍下?([0-9一二三四五六七八九十]{1,3})[元块]([0-9零一二三四五六七八九]{1,2})?$/u',
            $type, $m))
        {
            if (Number::parse($m[1], $yuan) && Number::parse($m[2], $fen))
                $price = $yuan '.' $fen;
        }
        elseif (preg_match( '/^拍下?([0-9{1,3}(.[0-9]{1,2}))[元块]$/u', $type, $m))
        {
            $price = $m[1];
        }
        if (isset($price)) $promo = array('price' => $price, 'type' => '拍下改价');
    }

    static function get_price_info($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer='http://detail.tmall.com/item.htm?id=' . $num_iid;
        $url='http://mdskip.taobao.com/core/initItemDetail.htm?queryMaybach=true&itemId=' . $num_iid;
        $response = $curl->get($url, $refer);

        $data = decode_json(iconv('GBK', 'UTF-8', $response->body));
        if ( isset($data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            ($price_info = $data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            is_array($price_info)
        ) return $price_info;
    }

    static function get_promo_info2($num_iid)
    {
        if (($result = TaobaoApi::ump_promotion_get($num_iid)) &&
            isset($result['ump_promotion_get_response']['promotions']
            ['promotion_in_item']['promotion_in_item'][0]) &&
            ($promo_info = $result['ump_promotion_get_response']['promotions']
            ['promotion_in_item']['promotion_in_item'][0])
        )
        {
            $item_info['promo_price'] = parse_price($promo_info['item_promo_price']);
            $item_info['promo_start'] = $promo_info['start_time'];
            $item_info['promo_end']   = $promo_info['end_time'];
        }
    }

    static function get_vip_price2($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer = 'http://item.taobao.com/item.htm?id=' . $num_iid;
        $url   = 'http://ajax.tbcdn.cn/json/umpStock.htm?itemId=' . $num_iid . '&p=1&rcid=28&sts=341317634,1170940438677291012,1225260582244974720,1166502676530463747&chnl=pc&price=9990&sellerId=10142375&shopId=&cna=M5sSCm8Hwi0CAbaW6nJssBNK&ref=&buyerId=174294739&nick=bughou&tg=1316864&tg2=67108872&tg3=1224979098644774912&tg4=4573968373776384&tg6=0';
        $response = $curl->get($url, $refer);
        if (! preg_match('/TB\.PromoData\s*=\s*({.*})/s', $response->body, $matches))
        {
            error_log('unexpected response:' . $response->body);
            return;
        }
        $data = decode_json(iconv('GBK', 'UTF-8', $matches[1]));
        $vip_price = null;
        if (is_array($data)) foreach($data as $sku)
            if (is_array($sku)) foreach($sku as $promo)
                if (is_array($promo) &&
                    isset($promo['type']) && $promo['type'] === 'VIP价格' &&
                    isset($promo['price']) && ($price = parse_price($promo['price'])) &&
                    (is_null($vip_price) || $price < $vip_price)
                ) $vip_price = $price;
        return $vip_price;
    }
}


