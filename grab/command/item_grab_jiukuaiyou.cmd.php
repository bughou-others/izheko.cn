<?php
require_once APP_ROOT . '/model/item_grab.model.php';

class ItemGrabJiukuaiyou extends ItemGrab
{
    const next_page_xpath = '//div[@class="page"]/div[@class="pageNav"]/a[@class="pg-next"]/@href';
    const item_node_xpath = '//ul[@class="goods-list"]/li/div';
    const item_jump_xpath  = './div[@class="good-price"]/a[@href]';
    const item_price_xpath = './div[@class="good-price"]/span[@class="price-current"]';
    const item_pic_xpath   = './div[@class="good-pic"]//img';
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
}

ItemGrabJiukuaiyou::start();

