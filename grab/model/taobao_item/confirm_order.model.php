<?php
require_once APP_ROOT . '/helper/curl.helper.php';

class ConfirmOrder
{
    static function init_curl(&$curl)
    {
        $curl = new Curl();
        $cookie_path = APP_ROOT . '/tmp/cookie.txt';
        curl_setopt_array($curl->curl, array(
            CURLOPT_COOKIEFILE => $cookie_path,
            CURLOPT_COOKIEJAR  => $cookie_path,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
#            CURLOPT_PROXY   => '192.168.2.3:8888',
#            CURLOPT_VERBOSE => true
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
        if($repeat && $status === 302) {
            echo 'login failed', PHP_EOL;
            echo iconv('GBK', 'UTF-8', $response->body);
            return self::login($curl, false);
        }
        if($status === 200) self::jump($curl);
    }

    static function jump($curl)
    {
        $url = 'http://www.tmall.com/';
        $curl->get('http://jump.taobao.com/jump?target=' . urlencode($url), $url);
        $pass = curl_getinfo($curl->curl, CURLINFO_REDIRECT_URL);
        if($pass && preg_match('@^http://pass.tmall.com/@i', $pass)) {
            $curl->get($pass, $url);
        } else {
            echo 'unexpected jump redirect url: ', $pass, PHP_EOL;
        }
    }

    static function get_price($num_iid, $skuid, $tmall)
    {
        static $curl;
        if (!$curl) self::init_curl($curl);

        $data = "quantity=1&item_id=$num_iid&skuId=$skuid";
        if($tmall) {
            $data  .= "&buy_param={$num_iid}_1_{$skuid}";
            $target = 'http://buy.tmall.com/order/confirm_order.htm';
            $refer  = 'http://detail.tmall.com/item.htm?id=' . $num_iid;
        } else {
            $target = 'http://buy.taobao.com/auction/buy_now.jhtml';
            $refer = 'http://item.taobao.com/item.htm?id=' . $num_iid ;
        }
        $response = $curl->post($target, $data, $refer);
        $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);

        if($status === 302 && ($url = curl_getinfo($curl->curl, CURLINFO_REDIRECT_URL))
            && preg_match('@^https?://(login|jump)\.(tmall|taobao)\.com@i', $url)
        ) {
            self::login($curl);
            $response = $curl->post($target, $data, $refer);
            $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
        }

        if ($status === 200) {
            if(preg_match($tmall ? '/"sum":(\d+),/' : '/"averageSum":"(\d+)"/', $response->body, $m)){
                return $m[1];
            } else {
                echo $num_iid, ': no price found in confirm page', PHP_EOL;
                echo iconv('GBK', 'UTF-8', $response->body);
            }
        } elseif ($url = curl_getinfo($curl->curl, CURLINFO_REDIRECT_URL)) {
            echo $num_iid, ': unexpected ' . $status . ' redirect: ', $url, PHP_EOL;
            echo iconv('GBK', 'UTF-8', $response->body);
        } else {
            echo $num_iid, ': unexpected status: ', $status, PHP_EOL;
            echo iconv('GBK', 'UTF-8', $response->body);
        }
    }
}


