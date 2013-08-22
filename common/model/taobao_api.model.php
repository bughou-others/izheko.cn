<?php
class TaobaoApi
{
    /* 沙箱环境调用地址
    const BASE_URL   = 'http://gw.api.tbsandbox.com/router/rest?';
    const APP_KEY    = 'test';
    const APP_SECRET = 'test';
     */
    const BASE_URL   = 'http://gw.api.taobao.com/router/rest?';
    const APP_KEY    = '21567955';
    const APP_SECRET = 'a4c10f52fcca718062745321bc927ed0';
    const NICK       = 'bughou';

    static function get_data($method, $app_params){
        $url = self::get_url($method, $app_params);
        $result = file_get_contents($url);
        return json_decode($result, true);
    }

    static function get_url($method, $app_params) {
        $sys_params = array(
            'method'      => $method,
            'app_key'     => self::APP_KEY,
            'format'      => 'json',
            'v'           => '2.0',
            'sign_method' => 'md5',
            'timestamp'   => date('Y-m-d H:i:s'),
        );
        $params = array_merge($sys_params, $app_params);
        $params['sign'] = self::get_sign($params);
        return self::BASE_URL . self::get_query_string($params);
    }

    static function get_sign($params) {
        $string = self::APP_SECRET;
        ksort($params);
        foreach ($params as $key => $value) {
            if ($key !== '' && $value !== '') {
                $string .= $key . $value;
            }
        }
        $string .= self::APP_SECRET;
        return strtoupper(md5($string));
    }

    static function get_query_string($params) {
        $string = '';
        foreach ($params as $key => $value) {
            if ($key !== '' && $value !== '') {
                $string .= $key . '=' . urlencode($value) . '&';
            }
        }
        return $string;
    }

    static function taobaoke_items_get() {
        return self::get_data('taobao.taobaoke.items.get', array(
            'fields'  => 'num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location,volume',
            'nick'    => 'bughou',
            'keyword' => '卫衣',
        ));
    }

    static function item_get($num_iid) {
        return self::get_data('taobao.item.get', array(
            'fields'   => 'num_iid,cid,title,pic_url,price,list_time,delist_time,freight_payer,post_fee,express_fee,ems_fee,auction_point',
            'num_iid'  => $num_iid,
        ));
    }

    static function ump_promotion_get($num_iid) {
        return self::get_data('taobao.ump.promotion.get', array( 'item_id' => $num_iid ));
    }

    static function taobaoke_items_detail_get($num_iids) {
        return self::get_data('taobao.taobaoke.items.detail.get', array(
            'fields'   => 'num_iid,click_url',
            'nick'     => self::NICK,
            'num_iids' => $num_iids,
        ));
    }

    static function taobaoke_items_coupon_get($keyword, $cid) {
        return self::get_data('taobao.taobaoke.items.coupon.get', array(
            'nick'     => self::NICK,
            'cid'      => $cid,
            'keyword'  => $keyword,
            'fields'   => 'num_iid,click_url,commission,commission_rate,commission_num,commission_volume,coupon_price,coupon_rate,coupon_start_time,coupon_end_time',
        ));
    }
    
    static function itemcats_get($cids) {
        return self::get_data('taobao.itemcats.get', array(
            #'fields'   => '',
            #'parent_cid'      => 0,
            'cids'      => $cids,
        ));
    }

    static function itemcats_children_get($cid) {
        return self::get_data('taobao.itemcats.get', array(
            #'fields'   => '',
            'parent_cid'      => $cid,
        ));
    }
}

