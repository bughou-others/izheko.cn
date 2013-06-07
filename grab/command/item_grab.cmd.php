<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/model/click_url_to_item_id.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemGrab
{
    static function start()
    {
        global $argv;
        $target = isset($argv[1]) ? $argv[1] : null;
        if ($target === null || $target === 'today')
            $url = 'http://ju.jiukuaiyou.com/jiu/all/today/new/all';
        elseif ($target === 'tomorrow')
            $url = 'http://ju.jiukuaiyou.com/r/'. strftime('%Y%m%d', strtotime('tomorrow'));
        elseif ($target === 'jiu')
            $url = null;
        elseif ($target === 'shijiu')
            $url = 'http://ju.jiukuaiyou.com/shijiu';
        else
        {
            error_log("unknown $target \nusage: {$argv[0]} [tomorow|today|jiu|shijiu]\n");
            return;
        }
        self::grab($url);
        system('cd ' . APP_ROOT . <<<EOL
; php run command/item_update.cmd.php >> tmp/item_update.log 2>&1 &
EOL
    );
    }

    static function grab($url)
    {
        echo strtotime("%F %T\n");
        $curl = new Curl();
        $refer = 'http://www.jiukuaiyou.com/';
        $page = $curl->get($refer);
        if ($url) $page = $curl->get($url, $refer);

        $xpath = '//div[@class="main"]//div[@class="page"]/div[@class="pageNav"]/a[@class="pg_next"]/@href';
        do {
            self::get_all_item($page);
        } while ($page = $page->get($xpath));
    }

    static function get_all_item($page)
    {
        $items = array();
        $node_list = $page->query('//div[@class="main"]//ul/li/div');
        foreach ($node_list as $item_node)
        {
            if($item = self::get_one_item($item_node, $page))
            {
                $item_id = array_shift($item);
                $items[$item_id] = $item;
            }
        }
        if (!$items) return;
        self::save_items($items);
    }

    static function get_one_item($item_node, $page)
    {
        $buy_node = $page->query(
            './div[@class="buy_content"]/div[@class="buy_action clearfix"]',
            $item_node)->item(0);
        if(!$buy_node)return;
        $jump_url = $page->query('./a', $buy_node)->item(0)->getAttribute('href');
        list($item_id, $has_click_url) = self::get_item_id($jump_url, $page); 
        $item_id = trim($item_id);
        if ($item_id && preg_match('/^\d+$/', $item_id))
        {
            $price = $page->query('./span[@class="price"]', $buy_node)->item(0)->nodeValue;
            $price = preg_match('/\d+(\.\d+)?/', $price, $matches) ? $matches[0] : null;
            $is_vip_price = $page->query('./h3/i[@class="tao_v"]', $item_node)->length > 0;
            return array($item_id, $price, $is_vip_price, $has_click_url, $jump_url);
        }
    }

    static function get_item_id($jump_url, $page)
    {
        $refresh = $page->get_by_url($jump_url)->query('//meta[@http-equiv="refresh"]/@content')->item(0);
        if ($refresh && ($refresh = $refresh->value) && preg_match("/;url='(.+)'/", $refresh, $matches))
        {
            $url = $matches[1];
            if(preg_match('{http://s.click.taobao.com/}i', $url))
                return array(ClickUrlToItemId::fetch($url, $jump_url), true);
            #http://item.taobao.com/item.htm?id=17894105049
            else if(preg_match('{http://item.taobao.com/item.htm\?.*(?<=[?&])id=(\d+)}i', $url, $matches))
                return array($matches[1], false);
            else error_log("unexpected click url: $url\n");
        }
    }

    static function save_items($items)
    {
        if (! $items) return;
        $now = strftime('%F %T');
        $values = '';
        foreach ($items as $item_id => $item_info)
        {
            list($ref_price, $ref_price_vip, $has_click_url, $ref_url) = $item_info;
            $flags = $has_click_url ? (ItemBase::FLAGS_MASK_REF_CLICK_URL) : 0;
            if($ref_price_vip) $flags |= ItemBase::FLAGS_MASK_REF_PRICE_VIP;
            $ref_price = $ref_price ? parse_price($ref_price) : 'null';
            $ref_url = DB::escape($ref_url);
            $values .= ",($item_id, '$now', $flags, $ref_price, '$ref_url')";
        }
        $values = substr($values, 1);
        $sql = 'insert ignore into items (`num_iid`, `create_time`, `flags`, `ref_price`, `ref_url`)
            values ' . $values;
        $count = count($items);
        $affected = DB::affected_rows($sql);
        $now = strftime('%F %T');
        if($affected === false) error_log("$now insert failed: $count");
        else echo "$now insert success: $count, {$affected}\n";
    }
}

ItemGrab::start();

