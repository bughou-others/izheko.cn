<?php
require_once APP_ROOT . '/model/taobao_item.model.php';
require_once APP_ROOT . '/../common/model/category.model.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemUpdate
{
    static function start()
    {
        global $argv;
        $where = isset($argv[1]) ? " where {$argv[1]}" : null;
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
        {
            $now = strftime('%F %T');
            $num_iid = $item['num_iid'];
            if(!$info = TaobaoItem::get_item_info($num_iid))
            {
                error_log("$now $i $num_iid get item info failded\n");
                continue;
            }
            if(!$changes = self::get_changes($item, $info))
            {
                #echo "$now $i $num_iid not changed\n";
                continue;
            }
            if($affected = self::update_one_item($num_iid, $changes))
                echo "$now $i $num_iid update success: {$affected}\n";
            else echo "$now $i $num_iid update failed\n";
        }
        if(--$i) echo "$now finished updating $i item\n\n";
        else echo "\n";
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

    static function update_one_item($num_iid, $data)
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

