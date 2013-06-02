<?php
require_once APP_ROOT . '/../common/db.php';

class ItemClear
{
    static function start()
    {
        self::clear();
    }

    static function clear()
    {
        #delisted or risen price(too expensive than ref price)
        $common = '
            from items where delist_time < now() or 
            least(
                ifnull(price, 0xffffffff),
                ifnull(vip_price, 0xffffffff),
                ifnull(promo_price, 0xffffffff)
            ) > 1.2 * ref_price
            ';
        $sql    = 'replace into items_history select * ' . $common;
        $count1 = DB::affected_rows($sql);
        $sql    = 'delete ' . $common;
        $count2 = DB::affected_rows($sql);
        $now = strftime('%F %T');
        echo "$now cleared $count2 => $count1 \n";
    }
}

ItemClear::start();

