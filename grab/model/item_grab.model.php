<?php
require_once APP_ROOT . '/helper/curl.helper.php';
require_once APP_ROOT . '/model/click_url_to_item_id.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemGrab
{
    static function start()
    {
        list($target, $refer) = static::get_target();
        self::grab($target, $refer);
        system('cd ' . APP_ROOT . '; php run command/item_update.cmd.php');
    }

    static function grab($target, $refer)
    {
        echo strtotime("%F %T\n");
        $curl = new Curl();
        if($refer && $refer !== $target)
            $curl->get($refer);
        else $refer = null;
        $page = $curl->get($target, $refer);

        do {
            self::get_all_item($page);
        } while ($page = $page->get(static::next_page_xpath));
        echo "\n";
    }

    static function get_all_item($page)
    {
        $items = array();
        $node_list = $page->query(static::item_node_xpath);
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
        $item_id = self::get_item_id($item_node, $page);
        if ($item_id && preg_match('/^\d+$/', $item_id))
        {
            $price = $page->query(static::item_price_xpath, $item_node)->item(0)->nodeValue;
            $price = preg_match('/\d+(\.\d+)?/', $price, $matches) ? $matches[0] : null;
            $pic_node = $page->query(static::item_pic_xpath, $item_node)->item(0);
            self::fetch_pic($item_id, $pic_node, $page);
            return array($item_id, $price);
        }
    }

    static function get_item_id($item_node, $page)
    {
        list($url, $refer) = static::get_click_url($item_node, $page);
            
        if(preg_match('{http://s.click.taobao.com/}i', $url))
            return ClickUrlToItemId::fetch($url, $refer);
        else if(preg_match('{\.com/item\.htm\?(?:.*&)?id=(\d+)}i', $url, $m))
            return $m[1];
        else error_log("unexpected click url: $url\n");
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


