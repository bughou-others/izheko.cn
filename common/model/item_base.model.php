<?php
require_once APP_ROOT . '/../common/helper/db.helper.php';

class ItemBase
{
    const FLAGS_MASK_NO_REF_PIC    = 1;
    const FLAGS_MASK_POSTAGE_FREE  = 4;
    const FLAGS_MASK_VIP_PRICE     = 8;
    const FLAGS_MASK_CHANGE_PRICE  = 16;
    const FLAGS_MASK_TMALL         = 32;
    const FLAGS_MASK_ITEM_DELETED  = 128;

    const factor_price_risen       = 1.2;
    static $types = array(
        1 => '女装',
        2 => '男装',
        3 => '居家',
        4 => '母婴',
        5 => '鞋包',
        6 => '配饰',
        7 => '美食',
        8 => '数码家电',
        9 => '化妆品',
        10 => '文体'
    );

    static function mask_bits($bits, $mask, $bool)
    {
        return $bool ? ($bits | $mask) : ($bits & ~$mask);
    }

    static function pic_path($num_iid)
    {
        return 'pic/' . implode('/', str_split(substr($num_iid, 0, 4)))
            . '/' . $num_iid . '.jpg';
    }
}
