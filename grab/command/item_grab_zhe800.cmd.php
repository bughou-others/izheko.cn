<?php
require_once APP_ROOT . '/model/item_grab.model.php';

class ItemGrabZhe800 extends ItemGrab
{
    const next_page_xpath  = '//div[@class="list_page"]/span[@class="next"]/a/@href';
    const item_node_xpath  = '//div[@class="dealbox"]/div[starts-with(@class, "deal ")]';
    const item_jump_xpath  = './div/h2/a[@href]/@href';
    const item_price_xpath = './div/h4/span[1]/em';
    const item_pic_xpath   = './div/p/a/img';
    const click_url_xpath  = null;

    static function get_target()
    {
        global $argv;
        $target = isset($argv[1]) ? $argv[1] : null;
        if ($target === null || $target === 'all')
            $url = 'http://www.zhe800.com/';
        else
        {
            error_log("unknown $target \nusage: {$argv[0]} [all]\n");
            return;
        }
        return array($url, 'http://www.zhe800.com/');
    }

    static function get_click_url($item_node, $page)
    {
         $href = $page->query(static::item_jump_xpath, $item_node)->item(0);
         if ($href && ($url = $href->value)) {
             $url = str_replace('/ju/fan/', '/ju/deal/', $url);
             return array($page->get_redirect_url_by_url($url), $url);
         }
    }

}

ItemGrabZhe800::start();
