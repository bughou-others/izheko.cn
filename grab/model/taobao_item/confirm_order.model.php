<?php
require_once APP_ROOT . '/helper/curl.helper.php';

class ConfirmOrder
{
    static function init_curl(&$curl)
    {
        $curl = new Curl();
        curl_setopt_array($curl->curl, array(
            CURLOPT_COOKIEFILE => APP_ROOT . '/tmp/cookie.txt',
            CURLOPT_COOKIEJAR  => APP_ROOT . '/tmp/cookie.txt',
            CURLOPT_FOLLOWLOCATION => false,
            //CURLOPT_PROXY   => '192.168.2.3:8888',
            CURLOPT_VERBOSE => true
        ));
    }

    static function login($curl, $repeat = true)
    {
        $login_page = $curl->get('https://login.taobao.com/');
        $form = $login_page->query('//form[@id="J_StaticForm"]')->item(0);
        if(!$form) $form = $login_page->query('//form[@id="J_Form"]')->item(0);
        if(!$form) {
            echo 'no form found in login page', PHP_EOL;
            echo iconv('GBK', 'UTF-8', $login_page->body);
        }
        $response = $login_page->submit($form,  array(
            'TPL_username' => 'bughou',
            'TPL_password' => 'impy1311',
        ));
        $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
        if($repeat && $status === 302) return self::login($curl);
    }

    static function get_detail_page($curl, $tmall, $num_iid, &$detail_page, &$form, &$data)
    {
        $url = 'http://' . ($tmall ? 'detail.tmall' : 'item.taobao') . '.com/item.htm?id=' . $num_iid . 
            '&ali_trackid=2:mm_40339139_4152163_13484640';
        curl_setopt($curl->curl, CURLOPT_FOLLOWLOCATION, true);
        $detail_page = $curl->get($url);
        curl_setopt($curl->curl, CURLOPT_FOLLOWLOCATION, false);
        $form = $detail_page->query('//form[@id="J_FrmBid"]')->item(0);
        if(!$form) {
            echo 'no form found in detail page', PHP_EOL;
            echo iconv('GBK', 'UTF-8', $detail_page->body);
            return;
        }
        if($tmall && $form->getAttribute('action') === '')
            $form->setAttribute('action', 'http://buy.tmall.com/order/confirm_order.htm');
        $data = array('skuId' => self::get_cheapest_sku($detail_page));
        var_dump($data);
    }

    static function get_cheapest_sku($page)
    {
        if(!preg_match('/"skuMap":(.*\}\s*\})/sU', $page->body, $m)) return;
        $skumap = json_decode($m[1], true);
        $cheapest = array_shift($skumap);
        foreach($skumap as $sku){
            if ($sku['price'] < $cheapest['price'])
                $cheapest = $sku;
        }
        return $cheapest['skuId'];
    }

    static function get_price($num_iid, $tmall)
    {
        static $curl;
        if (!$curl) self::init_curl($curl);

        self::get_detail_page($curl, $tmall, $num_iid, $detail_page, $form, $data);
        if(!$form) return;
        $response = $detail_page->submit($form, $data);
        $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);

        if($status === 302 && ($url = curl_getinfo($curl->curl, CURLINFO_REDIRECT_URL))
            && preg_match($tmall ? '@^http://login.tmall.com/@' : '@^https://login.taobao.com/@', $url))
        {
            self::login($curl, $tmall);
            $response = $detail_page->submit($form, $data);
            $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
        }

        if ($status === 200) {
            if(preg_match($tmall ? '/"sum":(\d+),/' : '/"averageSum":"(\d+)"/', $response->body, $m)){
                echo $m[1];
            } else {
                echo 'no price found in confirm page', PHP_EOL;
                echo iconv('GBK', 'UTF-8', $response->body);
            }
        } elseif ($url = curl_getinfo($curl->curl, CURLINFO_REDIRECT_URL)) {
            echo 'unexpected ' . $status . ' redirect: ', $url, PHP_EOL;
            echo iconv('GBK', 'UTF-8', $response->body);
        } else {
            echo 'unexpected status: ', $status, PHP_EOL;
            echo iconv('GBK', 'UTF-8', $response->body);
        }
    }
}
#ConfirmOrder::get_price(26150288998, false);
ConfirmOrder::get_price(27206788636, true);


