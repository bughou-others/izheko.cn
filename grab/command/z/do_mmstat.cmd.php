<?php
require APP_ROOT . '/helper/curl.helper.php';

class DoMmstatCmd 
{
    static function start()
    {
        $curl = new Curl();
        $url  = 'http://log.mmstat.com/connect.6.1?appkey=21567955&cache=';
        $refer = 'http://www.izheko.cn/';
        curl_setopt($curl->curl, CURLOPT_HTTPHEADER, array(
            'Accept: image/png,image/*;q=0.8,*/*;q=0.5',
            'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',
        ));
        for ($i = 1; $i <= 1; $i ++) {
            $curl->get($url. time() . rand(100, 999), $refer);
            $status = curl_getinfo($curl->curl, CURLINFO_HTTP_CODE);
            if ($status !== 200) echo $status . ' ';
            if ($i % 10 === 0) echo $i . PHP_EOL;
            curl_setopt($curl->curl, CURLOPT_USERAGENT, Curl::user_agent_string());
        }
    }
}
DoMmstatCmd::start();

