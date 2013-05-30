<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemGrab
{
    static function start()
    {
        $instance = new self;
        $instance->grab();
    }

    function __construct()
    {
        $this->curl = new Curl();
    }

    function grab()
    {
        global $argv;
        $target = isset($argv[1]) ? $argv[1] : null;
        if ($target === null || $target === 'tomorrow')
            $url = 'http://ju.jiukuaiyou.com/r/'. strftime('%Y%m%d', strtotime('tomorrow'));
        elseif ($target === 'today')
            $url = 'http://ju.jiukuaiyou.com/jiu/all/today/new/all';
        elseif ($target === 'jiu')
            $url = null;
        elseif ($target === 'shijiu')
            $url = 'http://ju.jiukuaiyou.com/shijiu';
        else
        {
            fputs(STDERR, "unknown $target \nusage: {$argv[0]} [tomorow|today|jiu|shijiu]\n");
            return;
        }

        $refer = 'http://www.jiukuaiyou.com/';
        $page = $this->curl->get($refer);
        if ($url) $page = $this->curl->get($url, $refer);

        $xpath = '//div[@class="main"]//div[@class="page"]/div[@class="pageNav"]/a[@class="pg_next"]/@href';
        do {
            $this->get_all_item($page);
        } while ($page = $page->get($xpath));
    }

    function get_all_item($page)
    {
        $items = array();
        $node_list = $page->query('//div[@class="main"]//ul/li/div');
        foreach ($node_list as $item_node)
        {
            if ( ($buy_node = $page->query('./div[@class="buy_content"]/a', $item_node)->item(0)) &&
                ($jump_url = $buy_node->getAttribute('href')) &&
                (list($item_id, $is_click_url) = $this->get_one_item($jump_url, $page)) &&
                ($item_id = trim($item_id)) && preg_match('/^\d+$/', $item_id)
            )
            {
                $price =  ( ($price_node = $page->query('./span[1]', $buy_node)->item(0)) &&
                    ($price = $price_node->nodeValue) && preg_match('/\d+(\.\d+)?/', $price, $matches)
                ) ? $matches[0] : null;
                $is_vip_price = $page->query('./h3/i[@class="tao_v"]', $item_node)->length > 0;
                $items[$item_id] = array($price, $is_vip_price, $is_click_url, $jump_url);
            }
        }
        if (!$items) return;
        $this->save_items($items);
    }

    function get_one_item($jump_url, $page)
    {
        $refresh = $page->get_by_url($jump_url)->query('//meta[@http-equiv="refresh"]/@content')->item(0);
        if ($refresh && ($refresh = $refresh->value) && preg_match("/;url='(.+)'/", $refresh, $matches))
        {
            $url = $matches[1];
            if(preg_match('{http://s.click.taobao.com/}i', $url))
                return $this->click_url_to_item_id($url, $jump_url);
            #http://item.taobao.com/item.htm?id=17894105049
            else if(preg_match('{http://item.taobao.com/item.htm\?.*(?<=[?&])id=(\d+)}i', $url, $matches))
                return array($matches[1], false);
            else fputs(STDERR, "unexpected click url: $url\n");
        }
    }

    #click_url example(302 redirected to url2)
    #http://s.click.taobao.com/t?e=zGU34CA7K%2BPkqB07S4%2FK0CITy7klxn%2Fr3HZwuuY0VC7BwYarcZXIp9bMsJoojiel33mlHXMmbJHHucXDti114cIfXvSeMfG7BZmPu7U6%2BMtR29z3OHhr5kQv9YudEycyfvIEXsdUNhOEonURMZavJou%2Bgj9J1EHiypv5SIW8VrveWHUmCrg7A9q%2F9FfKMkca%2FleUkRo%3D&spm=2014.12057478.1.0&u=108kh5101010101010nmekf9101010T0 
    #url2 example(javascript jump to url3)
    #http://s.click.taobao.com/t_js?tu=http%3A%2F%2Fs.click.taobao.com%2Ft%3Fe%3DzGU34CA7K%252BPkqB07S4%252FK0CFcRfH0G7DLkP9xIxJLW2WdpnlmHlSOtQyCItqeryZPm2FQwFfM8puBXmT43I0RsdE%252BrcuWQsTgswUiAxeRQvmDJFFfh8P2YtU%252BN2jvESW6ThoFok87jMLIq8TQUnBTbPSVk%252BNkaDGFpEQi8XTDfKzQIJhs8dPyRq6Xf1vsVjJAyzEEA5I%253D%26spm%3D2014.12057478.1.0%26u%3D108kh5101010101010nmido0101010T0%26ref%3Dhttp%253A%252F%252Fju.jiukuaiyou.com%252Fjump%252F10wphy%26et%3DjFBC59HfJ7EkJg%253D%253D
    #url3 example(302 redirected to url4)
    #http://s.click.taobao.com/t?e=zGU34CA7K%2BPkqB07S4%2FK0CFcRfH0G7DLkP9xIxJLW2WdpnlmHlSOtQyCItqeryZPm2FQwFfM8puBXmT43I0RsdE%2BrcuWQsTgswUiAxeRQvmDJFFfh8P2YtU%2BN2jvESW6ThoFok87jMLIq8TQUnBTbPSVk%2BNkaDGFpEQi8XTDfKzQIJhs8dPyRq6Xf1vsVjJAyzEEA5I%3D&spm=2014.12057478.1.0&u=108kh5101010101010nmido0101010T0&ref=http%3A%2F%2Fju.jiukuaiyou.com%2Fjump%2F10wphy&et=jFBC59HfJ7EkJg%3D%3D
    #url4 example
    #http://detail.tmall.com/item.htm?id=18263930351&ali_trackid=2:mm_16674950_0_0,108kh5101010101010nmido0101010T0:1368018253_3k1_855976017&spm=2014.12057478.1.0

    function click_url_to_item_id($click_url, $refer)
    {
        if (! $url2 = $this->curl->get_redirect_url($click_url, $refer))
        {
            fputs(STDERR, "no url2 from click_url: $click_url\n");
            return;
        }
        parse_str(parse_url($url2, PHP_URL_QUERY), $params);
        if (! $url3 = @$params['tu'])
        {
            fputs(STDERR, "no url3 from url2: $url2\n");
            return;
        }
        if (! $url4 = $this->curl->get_redirect_url($url3, $url2))
        {
            fputs(STDERR, "no url4 from url3: $url3\n");
            return;
        }
        parse_str(parse_url($url4, PHP_URL_QUERY), $params);
        if ($item_id = @$params['id'])
        {
            if (($item_id = trim($item_id)) && preg_match('/^\d+$/', $item_id))
                return array($item_id, true);
            else fputs(STDERR, "invalid item id $item_id from url4: $url4\n");
        }
        else
        {
            fputs(STDERR, "no item id from url4: $url4\n");
            return;
        }
    }

    function save_items($items)
    {
        if (! $items) return;
        $now = strftime('%F %T');
        $values = '';
        foreach ($items as $item_id => $item_info)
        {
            list($ref_price, $ref_price_vip, $is_click_url, $ref_url) = $item_info;
            $flags = $is_click_url ? (ItemBase::FLAGS_MASK_REF_CLICK_URL) : 0;
            if($ref_price_vip) $flags |= ItemBase::FLAGS_MASK_REF_PRICE_VIP;
            $ref_price = $ref_price ? parse_price($ref_price) : 'null';
            $ref_url = DB::escape($ref_url);
            $values .= ",($item_id, '$now', $flags, $ref_price, '$ref_url')";
        }
        $values = substr($values, 1);
        $sql = 'insert ignore into items (`num_iid`, `create_time`, `flags`, `ref_price`, `ref_url`)
            values ' . $values;
        $count = count($items);
        $affected = DB::affected_rows($sql)
        if ($affected == false) error_log("insert failed: $count\n");
        else echo "insert success: $count, {$affected}\n";
    }
}

ItemGrab::start();

