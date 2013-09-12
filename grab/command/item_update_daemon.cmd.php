<?php
class ItemUpdateDaemonCmd
{
    static function daemonize()
    {
        if(($pid = pcntl_fork()) > 0) exit;
        elseif($pid === 0)
        {
            posix_setsid();
            require_once APP_ROOT . '/../common/helper/db.helper.php';
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
        $pids = `pgrep -d' ' -fx 'php run command/item_update_daemon\.cmd\.php'`;
        $pid  = posix_getpid();
        $pids = preg_replace("/\b$pid\b/", '',  $pids);
        if(!preg_match('/^\s*$/', $pids)) `kill -TERM $pids`;
        pcntl_signal(SIGTERM, 'ItemUpdateDaemonCmd::sig_handler');
        self::main_loop();
    }

    static function main_loop()
    {
        while (true) {
            $now = time();
            self::do_work($now);
            self::do_sleep($now);
        }
    }

    static function do_work($now)
    {
        echo strftime("%F %T\n", $now);
        $cond = self::get_cond($now);
        system('cd ' . APP_ROOT .
            "; php run command/item_update.cmd.php '$cond' >> tmp/item_update_daemon.log 2>&1 &");
    }

    static function do_sleep($now)
    {
        declare(ticks = 1);
        $next = $now + 60;
        $cond = self::get_cond($next);
        $sql  = "select 1 from items where $cond limit 1";
        if (! DB::get_value($sql)) {
            $next = self::get_next($next);
        }
        if ($next) {
            while (($wait = $next - time()) > 0) sleep($wait);
        } else {
            $now = strftime('%F %T');
            exit("$now no one to update, exited\n");
        }
    }

    static function get_cond($time)
    {
        $start = strftime('%F %T', $time - 120);
        $end   = strftime('%F %T', $time + 120);
        $time_cond  = "between \"$start\" and \"$end\"";
        return "(start_time $time_cond or    end_time $time_cond or
                  list_time $time_cond or delist_time $time_cond ) ";
    }

    static function get_next($time)
    {
        $s = strftime('%F %T', $time);
        $sql = "
            select unix_timestamp(min(t)) from (
                select min(start_time)  t from items where start_time  > '$s'
                union all
                select min(end_time)    t from items where end_time    > '$s'
                union all
                select min(list_time)   t from items where list_time   > '$s'
                union all
                select min(delist_time) t from items where delist_time > '$s'
            ) tmp
         ";
        return DB::get_value($sql);
    }
}
ItemUpdateDaemonCmd::start();

