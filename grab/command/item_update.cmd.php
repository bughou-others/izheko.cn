<?php
require_once APP_ROOT . '/model/taobao_item.model.php';
require_once APP_ROOT . '/../common/model/category.model.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemUpdate
{
    static $list_time_change = false;
    static $changes_type_id  = array();

    static function start()
    {
        global $argv;
        $where = isset($argv[1]) ? $argv[1] : null;
        if($where === null) $where = ' where title=""';
        elseif($where === 'all') $where = '';
        else $where = "where $where";

        $sql = "select num_iid, title, flags, cid, type_id, price, vip_price, promo_price,
            promo_start, promo_end, list_time, delist_time, detail_url, pic_url
            from items $where order by id asc"; 
        DB::$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        self::update(DB::query($sql));
    }

    static function update($result)
    {
        $now = strftime('%F %T');
        echo "$now {$result->num_rows} item to update\n";

        for($i = 1; $item = $result->fetch_assoc(); $i++)
            self::update_one($item, $i);
        if(--$i) echo "$now finished updating $i item\n";

        if(self::$list_time_change)
        {
            echo "restart click_url_daemon\n";
            system('cd ' . APP_ROOT . <<<EOL
; php run command/click_url_daemon.cmd.php >> tmp/click_url_daemon.log 2>&1 &
EOL
        );
        }

        if(self::$changes_type_id)
        {
            require_once APP_ROOT . '/model/cache.model.php';
            Cache::clear(array_keys($changes_type_id));
        }
        echo "\n";
    }
    
    static function update_one($item, $i)
    {
        $now = strftime('%F %T');
        $num_iid = $item['num_iid'];
        if(!$info = TaobaoItem::get_item_info($num_iid))
        {
            error_log("$now $i $num_iid get item info failded");
            return;
        }
        if(!$changes = self::get_changes($item, $info)) return;

        if($item['title'] === '') $json = 'new';
        else $json = json_encode($changes);

        if($affected = self::update_db($num_iid, $changes))
            echo "$now $i $num_iid update success: {$affected} $json\n";
        else echo "$now $i $num_iid update failed $json\n";

        if(isset($changes['list_time'])) self::$list_time_change = true;
        self::$changes_type_id[$item['type_id']] = 1;
        if(isset($changes['type_id'])) self::$changes_type_id[$changes['type_id']] = 1;
    }

    static function get_changes($item, $info)
    {
        unset($item['num_iid']);
        $info['type_id'] = Category::get_type_id($info['cid']);
        $info['flags'] = self::mask_bits($item['flags'], ItemBase::FLAGS_MASK_POSTAGE_FREE,
            $info['freight_payer'] === 'seller' ||
            $info['post_fee']      === '0.00'   ||
            $info['express_fee']   === '0.00'   ||
            $info['ems_fee']       === '0.00'
        );
        $changes = array();
        foreach($item as $k => $v)
        {
            if(($v_new = array_key_exists($k, $info) ? $info[$k] : null) !== $v)
                $changes[$k] = $v_new;
        }
        return $changes;
    }

    static function update_db($num_iid, $data)
    {
        $data['update_time'] = strftime('%F %T');
        $sql = "update items set %s where num_iid = $num_iid ";
        return DB::update_affected_rows($sql, $data);
    }

    static function mask_bits($bits, $mask, $bool)
    {
        return $bool ? ($bits | $mask) : ($bits & ~$mask);
    }
}

ItemUpdate::start();

