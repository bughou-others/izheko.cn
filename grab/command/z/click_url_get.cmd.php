<?php
require_once APP_ROOT . '/model/click_url_get.model.php';

class ClickUrlGetCmd
{
    static function start()
    {
        global $argv;
        $target = isset($argv[1]) ? $argv[1] : null;
        if($target === 'all')
            $where = '';
        elseif($target === null || $target === 'blank')
            $where = 'where click_url=""';
        elseif(preg_match('/^(\d+),(\d+)$/', $target, $matches))
        {
            $now = time();
            $start = strftime('%F %T', $now - $matches[1] * 60);
            $end   = strftime('%F %T', $now + $matches[2] * 60);
            $where = "where click_url='' and list_time between '$start' and '$end'";
        }
        elseif($target) $where = "where $target";
        else die("usage: php run {$argv[0]} [blank|all|<minutes>,<minutes>]");

        ClickUrlGet::fetch($where);
    }
}
ClickUrlGetCmd::start();

