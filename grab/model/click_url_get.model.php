<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/../common/model/taobao_api.model.php';

class ClickUrlGet
{
    static function fetch($where)
    {
        $sql = "select num_iid from items $where order by id asc"; 
        $result = DB::query($sql);
        $now = strftime('%F %T');
        echo "$now {$result->num_rows} item to get click_url\n";
        $batch = array();
        while(list($num_iid) = $result->fetch_row())
        {
            $batch[] = $num_iid;
            if(count($batch) >= 10)
            {
                self::fetch_batch($batch);
                $batch = array();
            }
        }
        if($batch) self::fetch_batch($batch);
        $now = strftime('%F %T');
        echo "$now finished {$result->num_rows} item\n";
    }

    static function fetch_batch($num_iid_array)
    {
        if(!$click_url_array = self::get_batch($num_iid_array))return;
        static $n = 0;
        $i = 0;
        foreach($click_url_array as $num_iid => $click_url)
        {
            $i++;
            $now = strftime('%F %T');
            if($click_url){
                $affected = DB::update_affected_rows(
                    "update items set click_url='%s' where num_iid=$num_iid", $click_url);
                echo "$now $n-$i $num_iid : $affected\n";
            }
            else echo "$now $n-$i $num_iid : $click_url\n";
        }
        $n++;
    }

    static function get_batch($num_iid_array)
    {
        if (!$result = TaobaoApi::taobaoke_items_detail_get(implode(',', $num_iid_array)))return;
        if (!isset($result['taobaoke_items_detail_get_response']
            ['taobaoke_item_details']['taobaoke_item_detail']) ||
            (!$info = $result['taobaoke_items_detail_get_response']
            ['taobaoke_item_details']['taobaoke_item_detail'])
        ) return;
        $click_urls = array();
        foreach ($info as $one)
        {
            $click_urls[$one['item']['num_iid']] = $one['click_url'];
        }
        return $click_urls;
    }
}

