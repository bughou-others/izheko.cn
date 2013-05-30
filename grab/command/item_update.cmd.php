<?php
require_once APP_ROOT . '/model/taobao_item.model.php';
require_once APP_ROOT . '/../common/model/category.model.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class ItemUpdate
{
    static function start()
    {
        $instance = new self;
        $instance->update();
    }

    function update()
    {
        $sql = 'select num_iid from items'; 
        global $argv;
        if (isset($argv[1])) $sql .= " where {$argv[1]}";
        $sql .= ' order by id asc';
        $result = DB::query($sql);
        echo "{$result->num_rows} item to update\n";
        $i = 0;
        while(list($num_iid) = $result->fetch_row())
        {
            $i++;
            if ( ($info = TaobaoItem::get_item_info($num_iid)) &&
                ($affected = $this->update_item($num_iid, $info))
            )    echo "$i update $num_iid success: {$affected}\n";
            else echo "$i update $num_iid failed\n";
        }
    }

    function update_item($num_iid, $info)
    {
        $now         = strftime('%F %T');
        $title       = DB::escape($info['title']);
        $detail_url  = DB::escape($info['detail_url']);
        $click_url   = isset($info['click_url']) ? DB::escape($info['click_url']) : '';
        $pic_url     = DB::escape($info['pic_url']);
        $flags_operation = self::bits_update(ItemBase::FLAGS_MASK_POSTAGE_FREE,
            $info['freight_payer'] === 'seller' ||
            $info['post_fee']      === '0.00' ||
            $info['express_fee']   === '0.00' ||
            $info['ems_fee']       === '0.00'
        );
        $cid = $info['cid'];
        $type_id = Category::get_type_id($cid);
        $vip_price   = isset($info['vip_price'])   ? $info['vip_price']   : '0';
        $promo_price = isset($info['promo_price']) ? $info['promo_price'] : '0';
        $promo_start = isset($info['promo_start']) ? $info['promo_start'] : '0';
        $promo_end   = isset($info['promo_end'])   ? $info['promo_end']   : '0';

        $sql = "update items set update_time='$now',
            title='$title', flags=flags $flags_operation,
            cid='$cid', type_id='$type_id',
            price='{$info['price']}', vip_price='$vip_price',
            promo_price='$promo_price',
            promo_start='$promo_start',
            promo_end  ='$promo_end',
            list_time  ='{$info['list_time'  ]}',
            delist_time='{$info['delist_time']}',
            detail_url ='$detail_url',
            click_url  ='$click_url',
            pic_url    ='$pic_url'
            where num_iid = $num_iid ";
        return DB::affected_rows($sql);
    }

    static function bits_update($bits, $bool)
    {
        return $bool ? ('| ' . $bits) : ('& ' . (~ $bits));
    }

}
ItemUpdate::start();


