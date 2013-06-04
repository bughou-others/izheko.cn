<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/model/click_url_get.model.php';

class ClickUrlDaemonCmd
{
    const error_limit = 60;

    static function daemonize()
    {
        if(($pid = pcntl_fork()) > 0) exit;
        elseif($pid === 0) posix_setsid();
        else  error_log('daemonize faild');
    }

    static function sig_handler($signo)
    {
        exit("signal $signo, exited\n");
    }

    static function start()
    {
        self::daemonize();
        `pkill -SIGTERM -fx 'php run command/click_url_daemon\.cmd\.php'`;
        pcntl_signal(SIGTERM, 'self::sig_handler');
    }

    static function main_loop()
    {
        do {
            $now = time();
            self::do_work($now, isset($last) ? $last : null);
            declare(ticks = 1) {
                self::do_sleep($now);
            }
            $last = $now;
        } while(true);
    }

    static function do_work($now, $last)
    {
        $s = strftime('%F %T', $now + self::error_limit);
        $where = "where click_url='' and list_time <= '$s'";
        if($last) {
            $s = strftime('%F %T', $last - self::error_limit);
            $where .= " and list_time >= $s";
        }
        ClickUrlGet::fetch($where);
    }

    static function do_sleep($now)
    {
        $s = strftime('%F %T', $now);
        $sql = "select max(list_time) from items where click_url='' and list_time<='$s'";
        if(($prev = DB::get_value($sql)) && $now < ($next = strtotime($prev) + self::error_limit)) ;
        else
        {
            $sql = "select min(list_time) from items where click_url='' and list_time>'$s'";
            if($next = DB::get_value($sql)) $next = strtotime($next);
            else exit("no one to get click_url\n");
        }
        if(($wait = $next - time()) > 0)sleep($wait);
    }
}
ClickUrlDaemonCmd::start();

