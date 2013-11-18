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
        1 => array('女装', 'nvzhuang'),
        2 => array('男装', 'nanzhuang'),
        3 => array('居家', 'jujia'),
        4 => array('母婴', 'muying'),
        5 => array('鞋包', 'xiebao'),
        6 => array('配饰', 'peishi'),
        7 => array('美食', 'meishi'),
        8 => array('数码家电', 'shumajiadian'),
        9 => array('化妆品', 'huazhuangpin'),
        10 => array('文体', 'wenti')
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
