<?php
Class PromoInfo
{
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
