<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/helper/json.helper.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';

class ItemPromo
{
    static function get_promo_info($num_iid)
    {
        list($postage_free, $price_info) = self::get_price_info($num_iid);
        $promo = null;
        if (is_array($price_info)) {
            foreach ($price_info as $sku)
            {
                if (isset($sku['promotionList']) &&
                    is_array($promo_list = $sku['promotionList'])
                )
                foreach($promo_list as $this_promo) {
                    if (($price = parse_price($this_promo['price'])) &&
                        (is_null($promo) || $price < $promo['price'])
                    ) {
                        $this_promo['price'] = $price;
                        $promo = $this_promo;
                    }
                }
            }
        }
        if ($promo) {
            $promo['price_type'] = isset($promo['type']) &&
                ($promo['type'] === 'VIP价格' || $promo['type'] === '店铺vip')
                ? 'VIP价格' : '';
        } else $promo = array();
        $promo['postage_free'] = $postage_free;
        return $promo;
    }

    static function get_price_info($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $refer = 'http://detail.tmall.com/item.htm?id=' . $num_iid;
        $url   = 'http://mdskip.taobao.com/core/initItemDetail.htm?queryMaybach=true&itemId=' . $num_iid;
        $response = $curl->get($url, $refer);
        $body = iconv('GBK', 'UTF-8', $response->body);

        $data = decode_json($body);
        if (!$data) {
            echo 'get promo info failed', PHP_EOL;
            var_dump(curl_getinfo($curl->curl, CURLINFO_HTTP_CODE));
            var_dump($body);
        }
        if ( isset($data['defaultModel']['deliveryDO']['deliverySkuMap']['default'][0]) &&
            ($post_info = $data['defaultModel']['deliveryDO']['deliverySkuMap']['default'][0]) &&
            is_array($post_info)
        ) $postage_free = strpos($post_info['postage'], ' 0.00 ') !== false || $post_info['postageFree'];
        else {
            echo 'no default post info', PHP_EOL;
            $postage_free = null;
        }

        if ( isset($data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            ($price_info = $data['defaultModel']['itemPriceResultDO']['priceInfo']) &&
            is_array($price_info)
        ) ;
        else {
            $price_info = null;
        }

        return array($postage_free, $price_info);
    }

    static function get_subtitle($num_iid)
    {
        static $curl;
        if (! $curl) $curl = new Curl();
        $url ='http://detail.tmall.com/item.htm?id=' . $num_iid . '&ali_trackid=2:mm_';
        $response = $curl->get($url);
        $response->fix_charset();
        if ($t = $response->query('//div[@id="J_DetailMeta"]/div[@class="tb-property"]/div[@class="tb-wrap"]/div[@class="tb-detail-hd"]/p')->item(0))
            return trim($t->nodeValue);
    }

}
