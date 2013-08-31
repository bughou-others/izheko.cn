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
        system('cd ' . APP_ROOT . '; php run command/item_update.cmd.php');
    }

    static function grab($url)
    {
        echo strtotime("%F %T\n");
        $curl = new Curl();
        $refer = 'http://www.jiukuaiyou.com/';
        $page = $curl->get($refer);
        if ($url) $page = $curl->get($url, $refer);

        $xpath = '//div[@class="page"]/div[@class="pageNav"]/a[@class="pg-next"]/@href';
        do {
            self::get_all_item($page);
        } while ($page = $page->get($xpath));
        echo "\n";
    }

    static function get_all_item($page)
    {
        $items = array();
        $node_list = $page->query('//ul[@class="goods-list"]/li/div');
        foreach ($node_list as $item_node)
        {
            if($item = self::get_one_item($item_node, $page))
            {
                $items[$item[0]] = $item[1];
            }
        }
        if (!$items) return;
        self::save_items($items);
    }

    static function get_one_item($item_node, $page)
    {
        $jump_url = $page->query('./div[@class="good-price"]/a[@href]', $item_node)->item(0);
        if (!$jump_url) {
            //echo $page->query('./h5', $item_node)->item(0)->nodeValue, PHP_EOL;
            return;
        }
        $jump_url = $jump_url->getAttribute('href');
        $item_id = self::get_item_id($jump_url, $page);
        $item_id = trim($item_id);
        if ($item_id && preg_match('/^\d+$/', $item_id))
        {
            $price = $page->query('./div[@class="good-price"]/span[@class="price-current"]',
                $item_node)->item(0)->nodeValue;
            $price = preg_match('/\d+(\.\d+)?/', $price, $matches) ? $matches[0] : null;
            self::fetch_pic($item_id, $page->query('./div[@class="good-pic"]//img', $item_node)->item(0), $page);
            return array($item_id, $price);
        }
    }

    static function fetch_pic($item_id, $img, $page)
    {
        $path = APP_ROOT . '/../static/public/' . ItemBase::pic_path($item_id);
        if(file_exists($path)) return;
        
        $pic = $img->getAttribute('data-original');
        if($pic === '') $pic = $img->getAttribute('src');
        if($pic === '') return;
        $response = $page->get_by_url($pic);

        if(!is_dir($dir = dirname($path))) mkdir($dir, 0755, true);
        file_put_contents($path, $response->body);
    }

    static function get_item_id($jump_url, $page)
    {
        $refresh = $page->get_by_url($jump_url)->query('//meta[@http-equiv="refresh"]/@content')->item(0);
        if ($refresh && ($refresh = $refresh->value) && preg_match("/;url='(.+)'/", $refresh, $matches))
        {
            $url = $matches[1];
            if(preg_match('{http://s.click.taobao.com/}i', $url))
                return ClickUrlToItemId::fetch($url, $jump_url);
            else if(preg_match('{http://item.taobao.com/item.htm\?.*(?<=[?&])id=(\d+)}i', $url, $matches))
                return $matches[1];
            else error_log("unexpected click url: $url\n");
        }
    }

    static function save_items($items)
    {
        if (! $items) return;
        $now = strftime('%F %T');
        $values = '';
        foreach ($items as $item_id => $ref_price)
        {
            $ref_price = $ref_price ? parse_price($ref_price) : 'null';
            $values .= ",($item_id, '$now', $ref_price)";
        }
        $values = substr($values, 1);
        $sql = 'insert ignore into items (`num_iid`, `create_time`, `ref_price`)
            values ' . $values;
        $count = count($items);
        $affected = DB::affected_rows($sql);
        $now = strftime('%F %T');
        if($affected === false) error_log("$now insert failed: $count");
        else echo "$now insert success: $count, {$affected}\n";
    }
}

ItemGrab::start();

