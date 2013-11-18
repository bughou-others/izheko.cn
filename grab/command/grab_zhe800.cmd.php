<?php
require_once APP_ROOT . '/model/item_grab.model.php';

class ItemGrabZhe800 extends ItemGrab
{
    const next_page_xpath  = '//div[@class="list_page"]/span[@class="next"]/a/@href';
    const item_node_xpath  = '//div[@class="dealbox"]/div[starts-with(@class, "deal ")]';
    const item_jump_xpath  = './div/h2/a[@href]/@href';
    const item_price_xpath = './div/h4/span[1]/em';
    const item_pic_xpath   = './div/p/a/img';
    const item_type_xpath   = './div/h2/strong/a';
    const item_tip_xpath   = './div/h6';
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

    static function get_ref_iid($item_node, $page)
    {
        $info = explode(',', $item_node->getAttribute('info'));
        if (isset($info[1])) return $info[1];
    }

    static function get_start_end_time($item_node, $page)
    {
        $info = explode(',', $item_node->getAttribute('info'));
        if (isset($info[3], $info[2])) 
            return array(
                strftime('%F %T', substr($info[3], 0, -3)),
                strftime('%F %T', substr($info[2], 0, -3))
            );
    }

    static function get_type_id($item_node, $page)
    {
        static $types;
        if ($types === null) $types = array_flip(ItemBase::$types);

        $type = $page->query(static::item_type_xpath, $item_node)->item(0)->nodeValue;
        if (preg_match('/【(.+)】/', trim($type), $m) && isset($types[$m[1]])) {
            return $types[$m[1]];
        }
        return 'null';
    }

    static function get_click_url($item_node, $page)
    {
         $href = $page->query(static::item_jump_xpath, $item_node)->item(0);
         if ($href && ($url = $href->value)) {
             $url = str_replace('/ju/fan/', '/ju/deal/', $url);
             return array($page->get_redirect_url_by_url($url), $url);
         }
    }

    static function get_tip_text($item_node, $page)
    {
        $tip = $page->query(static::item_tip_xpath, $item_node)->item(0)->nodeValue;
        $tip = trim($tip);
        $tip = preg_replace('/^小编：/u', '', $tip);
        $tip = preg_replace('/举报$/u', '', $tip);
        return trim($tip);
    }
}

ItemGrabZhe800::start();

