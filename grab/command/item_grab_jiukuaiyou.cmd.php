<?php
require_once APP_ROOT . '/model/item_grab.model.php';

class ItemGrabJiukuaiyou extends ItemGrab
{
    const next_page_xpath = '//div[@class="page"]/div[@class="pageNav"]/a[@class="pg-next"]/@href';
    const item_node_xpath = '//ul[@class="goods-list"]/li/div';
    const item_jump_xpath  = './div[@class="good-price"]/a[@href]/@href';
    const item_price_xpath = './div[@class="good-price"]/span[@class="price-current"]';
    const item_pic_xpath   = './div[@class="good-pic"]//img';
    const item_tip_xpath   = './h5[@class="good-title"]/a[2]';
    const click_url_xpath  = '//meta[@http-equiv="refresh"]/@content';

    static function get_target()
    {
        global $argv;
        $target = isset($argv[1]) ? $argv[1] : null;
        if ($target === null || $target === 'today')
            $url = 'http://ju.jiukuaiyou.com/jiu/all/today/new/all';
        elseif ($target === 'tomorrow')
            $url = 'http://ju.jiukuaiyou.com/r/'. strftime('%Y%m%d', strtotime('tomorrow'));
        elseif ($target === 'jiu') 
            $url = 'http://www.jiukuaiyou.com/';
        elseif ($target === 'shijiu')
            $url = 'http://ju.jiukuaiyou.com/shijiu';
        else
        {
            error_log("unknown $target \nusage: {$argv[0]} [tomorow|today|jiu|shijiu]\n");
            return;
        }
        return array($url, 'http://www.jiukuaiyou.com/');
    }

    static function get_click_url($item_node, $page)
    {
        $response = $page->get(static::item_jump_xpath, $item_node);
        if(!$response) return;
        $refresh = $response->query(static::click_url_xpath)->item(0);
        if($refresh && ($refresh = $refresh->value) && preg_match("/;url='(.+)'/", $refresh, $m))
            return array($m[1], $response->url);
    }

    static function get_tip_text($item_node, $page)
    {
        $tip = $page->query(static::item_tip_xpath, $item_node)->item(0)->nodeValue;
        return trim($tip);
    }
}

ItemGrabJiukuaiyou::start();

