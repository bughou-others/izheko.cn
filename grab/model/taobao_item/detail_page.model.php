<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/helper/json.helper.php';

class DetailPage
{
    static function get($num_iid) {
        static $curl;
        if (! $curl) $curl = new Curl();
        $url ='http://item.taobao.com/item.htm?id=' . $num_iid . '&ali_trackid=2:mm_40339139';
        $response = $curl->get($url);
        $code = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
        if ($code !== 200) {
            $response = $curl->get($url);
            $code = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
            if ($code !== 200) {
                echo 'unexpected ', $code, ' response';
                return;
            }
        }
        $response->fix_charset();

        $info = array();
        $url = curl_getinfo($curl->curl, CURLINFO_EFFECTIVE_URL);
        $tmall = preg_match('#^http://detail.tmall.com/#', $url) ? true : false;
        $info['tmall'] = $tmall;

        $title = $response->query('//div[@id="detail"]//div[@class="tb-detail-hd"]//h3')->item(0);
        if ($title) $info['title'] = trim($title->nodeValue);
        $pic = $response->query('//*[@id="J_ImgBooth"]')->item(0);
        
        if ($tmall) {
            if ($pic) $info['pic_url'] = $pic->getAttribute('src');
            if (preg_match('/"itemDO" : (\{[^}]+\})/', $response->body, $m)) {
                $item_do = decode_json($m[1], true);
                $info['price'] = $item_do['reservePrice'];
                $info['cid']   = (int)$item_do['categoryId'];
            }
        } else {
            if ($pic) $info['pic_url'] = preg_replace('/_\d+x\d+\.jpg$/', '', $pic->getAttribute('data-src'));
            $price = $response->query('//strong[@id="J_StrPrice"]/em[@class="tb-rmb-num"]')->item(0);
            if ($price) $info['price'] = $price->nodeValue;
            if (preg_match('/cid:\'(\d+)\',/', $response->body, $m)) {
                $info['cid'] = (int)$m[1];
            }
        }
        return $info;
    }
}

