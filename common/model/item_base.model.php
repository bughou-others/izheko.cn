<?php
require_once APP_ROOT . '/../common/db.php';

class ItemBase
{
    const FLAGS_MASK_REF_CLICK_URL = 1;
    const FLAGS_MASK_REF_PRICE_VIP = 2;
    const FLAGS_MASK_POSTAGE_FREE  = 4;
    const FLAGS_MASK_ITEM_DELETED  = 128;
}
