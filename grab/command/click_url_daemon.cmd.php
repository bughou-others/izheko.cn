<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/model/click_url_get.model.php';

class ClickUrlDaemonCmd
{
    const $error_limit = 300;

    static function start()
    {
        do {
            $now = time();
            self::do_work($now, isset($last) ? $last : null);
            self::wait($now);
            $last = $now;
        } while(true);
    }

    static function do_work($now, $last)
    {
        $s = strftime('%F %T', $now + $error_limit);
        $where = "where click_url='' and list_time <= '$s'";
        if($last) {
            $s = strftime('%F %T', $last - $error_limit);
            $where .= " and list_time >= $s";
        }
        ClickUrlGet::fetch($where);
    }

    static function get_next_time($now)
    {
        $s = strftime('%F %T', $now);
        $sql = "select min(list_time) from items where click_url='' and list_time>'$now'";
        if($next = DB::get_value($sql))
        {
            $sql = "select max(list_time) from items where click_url='' and list_time>'$last_s'";
            $next = strtotime($next);
            $next - $now;
        }
    }
}
ClickUrlDaemonCmd::start();

