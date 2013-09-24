<?php
require_once APP_ROOT . '/model/taobao_item/taobao_item.model.php';

declare(ticks = 1);
class ItemUpdate
{
    static $changes_type_id  = array();

    static function start()
    {
        global $argv;
        $condition = isset($argv[1]) ? $argv[1] : null;
        if ($condition === null)
            $condition = 'and title="" and !(flags&' . ItemBase::FLAGS_MASK_ITEM_DELETED . ')';
        elseif ($condition === 'dated') {
            $ago = strftime('%F %T', time() - 3600 * 8);
            $condition = "and update_time < '$ago'";
        }
        elseif ($condition === 'all') $condition = '';
        else $condition = "and $condition";

        $pid = posix_getpid();
        $sql = "update items set updater=$pid where updater=0 $condition";
        if(!DB::affected_rows($sql)){
            echo 'no item to update', PHP_EOL;
            return;
        }

        pcntl_signal(SIGINT, 'ItemUpdate::exit_callback');
        pcntl_signal(SIGTERM, 'ItemUpdate::exit_callback');
        register_shutdown_function('ItemUpdate::exit_callback');

        $sql = "select num_iid, title, flags, cid, type_id, price, now_price,
            start_time, end_time, list_time, delist_time, pic_url
            from items where updater=$pid order by id desc for update
            ";
        DB::$db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        self::update(DB::query($sql));
    }

    static function exit_callback($signo = null)
    {
        $now = strftime('%F %T');
        $pid = posix_getpid();
        $caller = $signo ? "signal $signo" : 'shutdown function';
        echo "$now clear updater $pid by $caller\n\n";
        $sql = "update items set updater=0 where updater=$pid";
        DB::query($sql);
        exit;
    }

    static function update($result)
    {
        $now = strftime('%F %T');
        echo "$now {$result->num_rows} item to update\n";

        for($i = 1; $item = $result->fetch_assoc(); $i++)
        {
            self::update_one($item, $i);
        }
        if (--$i) {
            $now = strftime('%F %T');
            echo "$now finished updating $i item\n";
        }

        if(self::$changes_type_id)
        {
            require_once APP_ROOT . '/model/cache.model.php';
            Cache::clear(array_keys(self::$changes_type_id));
        }
    }
    
    static function update_one($item, $i)
    {
        $now = strftime('%F %T');
        $num_iid = $item['num_iid'];
        if(!$info = TaobaoItem::get_item_info($num_iid))
        {
            error_log("$now $i $num_iid get item info failded");
            return;
        }
        $changes = self::get_changes($item, $info);

        if($info === 'deleted') $json = 'deleted';
        elseif($item['title'] === '') $json = 'new';
        else {
            $json = '{';
            foreach($changes as $k => $v) $json .= " $k: $v,";
            $json .= ' }';
        }
        
        if($affected = self::update_db($num_iid, $changes))
            echo "$now $i $num_iid update success: {$affected} $json\n";
        else echo "$now $i $num_iid update failed $json\n";

        if(isset($item['type_id'])) self::$changes_type_id[$item['type_id']] = 1;
        if(isset($changes['type_id'])) self::$changes_type_id[$changes['type_id']] = 1;
    }

    static function get_changes($item, $info)
    {
        if($info === 'deleted')
        {
            $flags = $item['flags'] | ItemBase::FLAGS_MASK_ITEM_DELETED;
            return $flags === $item['flags'] ? null : array('flags' => $flags);
        }
        unset($item['num_iid']);
        $changes = array();
        foreach($item as $k => $v)
        {
            if(($v_new = array_key_exists($k, $info) ? $info[$k] : null) !== $v)
                $changes[$k] = $v_new;
        }
        return $changes;
    }

    static function update_db($num_iid, $data)
    {
        $data['update_time'] = strftime('%F %T');
        $sql = "update items set updater=0, %s where num_iid = $num_iid ";
        return DB::update_affected_rows($sql, $data);
    }
}

ItemUpdate::start();

