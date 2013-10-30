<?php
require_once APP_ROOT . '/../common/helper/db.helper.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class CheckRefPic
{
    static function check_table($suffix = null) {
        $result = DB::query('select num_iid, flags from items' . $suffix);
        while (list($num_iid, $flags) = $result->fetch_row()) {
            $no_ref_pic = $flags & ItemBase::FLAGS_MASK_NO_REF_PIC;
            $pic = APP_ROOT . '/../static/public/' . ItemBase::pic_path($num_iid);
            if (is_file($pic) && filesize($pic) > 0) {
                if ($no_ref_pic) self::update_no_ref_pic_flag($num_iid, false, $suffix);
            } else {
                if (!$no_ref_pic) self::update_no_ref_pic_flag($num_iid, true, $suffix);
            }
        }
    }

    static function update_no_ref_pic_flag($num_iid, $flag, $suffix = null) {
        $op = ($flag ?  '| ' : '& ~') . ItemBase::FLAGS_MASK_NO_REF_PIC;
        $ok = DB::query("update items$suffix set flags = flags $op where num_iid = $num_iid");
        echo ($flag ? 'set ' : 'unset '), $num_iid, ($ok ? ' ok' : ' fail'), PHP_EOL;
    }
}
CheckRefPic::check_table($argv[1]);

