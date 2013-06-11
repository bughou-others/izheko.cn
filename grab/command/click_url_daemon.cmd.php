<?php
class ClickUrlDaemonCmd
{
    const error_limit = 60;

    static function daemonize()
    {
        if(($pid = pcntl_fork()) > 0) exit;
        elseif($pid === 0)
        {
            posix_setsid();
            require_once APP_ROOT . '/../common/helper/db.helper.php';
            require_once APP_ROOT . '/model/click_url_get.model.php';
        }
        else error_log('daemonize faild');
    }

    static function sig_handler($signo)
    {
        $now = strftime('%F %T');
        exit("$now signal $signo, exited\n");
    }

    static function start()
    {
        self::daemonize();
        $pids = `pgrep -d' ' -fx 'php run command/click_url_daemon\.cmd\.php'`;
        $pid  = posix_getpid();
        $pids = preg_replace("/\b$pid\b/", '',  $pids);
        if(!preg_match('/^\s*$/', $pids)) `kill -TERM $pids`;
        pcntl_signal(SIGTERM, 'ClickUrlDaemonCmd::sig_handler');
        self::main_loop();
    }

    static function main_loop()
    {
        do {
            $now = time();
            self::do_work($now, isset($last) ? $last : null);
            self::do_sleep($now);
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
        declare(ticks = 1);
        $s = strftime('%F %T', $now);
        $sql = "select max(list_time) from items where click_url='' and list_time<='$s'";
        if(($prev = DB::get_value($sql)) && $now < ($next = strtotime($prev) + self::error_limit)) ;
        else
        {
            $sql = "select min(list_time) from items where click_url='' and list_time>'$s'";
            if($next = DB::get_value($sql)) $next = strtotime($next);
            else 
            {
                $now = strftime('%F %T');
                exit("$now no one to get click_url, exited\n");
            }
        }
        if(($wait = $next - time()) > 0)sleep($wait);
    }
}
ClickUrlDaemonCmd::start();

