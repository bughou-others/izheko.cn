<?php
require_once APP_ROOT . '/helper/curl.helper.php';

class ConfirmOrder
{
    static $tb_token;
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
            //CURLOPT_PROXY   => '192.168.2.3:8888',
            CURLOPT_VERBOSE => true
        ));
        $cookie_file = fopen($cookie_path, 'r');
        while($line = fgets($cookie_file)) {
            $fields = explode("\t", $line);
            if(count($fields) === 7 && $fields[5] === '_tb_token_')
            {
                self::$tb_token = trim($fields[6]);
                break;
            }
        }
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
        curl_setopt($curl->curl, CURLOPT_HEADER, true);
        $response = $login_page->submit($form,  array(
            'TPL_username' => 'bughou',
            'TPL_password' => 'impy1311',
        ));
        curl_setopt($curl->curl, CURLOPT_HEADER, false);
        $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
        if($repeat && $status === 302) {
            echo iconv('GBK', 'UTF-8', $response->body);
            exit();
            //return self::login($curl, false);
        }
        if($status === 200) {
            $size = curl_getinfo($curl->curl, CURLINFO_HEADER_SIZE);
            $header = substr($response->body, 0, $size);
            if(preg_match('/^Set-Cookie: _tb_token_=([^;]*); /mi', $header, $m))
            {
                self::$tb_token = $m[1];
                var_dump($m[1]);
            }
            self::jump($curl);
        }
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

    static function get_detail_page($curl, $tmall, $num_iid, &$detail_page, &$form, &$data)
    {
        $url = 'http://' . ($tmall ? 'detail.tmall' : 'item.taobao') . '.com/item.htm?id=' . $num_iid . 
            '&ali_trackid=2:mm_';
        $detail_page = $curl->get($url);
        if($tmall && curl_getinfo($curl->curl, CURLINFO_HTTP_CODE) === 302) {
            self::login($curl);
            $detail_page = $curl->get($url);
        }
        $detail_page->fix_charset();
        $form = $detail_page->query('//form[@id="J_FrmBid"]')->item(0);
        if(!$form) {
            echo 'no form found in detail page', PHP_EOL;
            echo iconv('GBK', 'UTF-8', $detail_page->body);
            return;
        }
        if($tmall && $form->getAttribute('action') === '')
            $form->setAttribute('action', 'http://buy.tmall.com/order/confirm_order.htm');
        $data = array(
            'item_id' => $num_iid,
            'item_id_num' => $num_iid,
            'skuId' => self::get_cheapest_sku($detail_page)
        );
        if($tmall) $data['_tb_token_'] = self::$tb_token;
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
            && preg_match('@^https?://(login|jump)\.(tmall|taobao)\.com@i', $url)
        ) {
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


