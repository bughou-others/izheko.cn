<?php
require_once APP_ROOT . '/../common/db.php';
require_once APP_ROOT . '/../common/model/item_base.model.php';

class Item extends ItemBase
{
    static function select($type = null, $page = 0, $page_size = 30)
    {
        if($type) {
            $type_id = DB::get_value("select id from types where pinyin = '$type'");
            if(!$type_id) return;
            $type_condition = "and type_id=$type_id";
        }
        else $type_condition = '';

        $offset = $page >= 1 ? ($page - 1) * $page_size : 0;
        $sql = "select SQL_CALC_FOUND_ROWS title,flags,ref_price,price,promo_price,vip_price,
            promo_start,list_time,delist_time,detail_url,click_url,pic_url
            from items where title != '' $type_condition limit $offset, $page_size
            ";
        $result = DB::query($sql);
        $found_rows = DB::get_value('select found_rows()');
        $instances = array();
        while($data = $result->fetch_assoc()) $instances[] = new self($data);
        return array($instances, $found_rows);
    }

    function __construct($data)
    {
        $this->data = $data;
    }
    
    function get($name)
    {
        if (isset($this->data[$name])) return $this->data[$name];
    }

    function get_title()
    {
        return preg_replace('/^【[^】]*】/', '', $this->data['title']);
    }

    function get_pic_url()
    {
        #170 190 210 240
        return $this->data['pic_url'] . '_210x210.jpg';
    }
    
    function postage_free()
    {
        return $this->data['flags'] & self::FLAGS_MASK_POSTAGE_FREE;
    }

    function get_price_and_status()
    {
        list($discount_price, $vip, $original_price, $risen_price) = $this->get_price();
        $now = time();
        if ($now < strtotime($this->data['list_time']) || $now < strtotime($this->data['promo_start']))
            $status = '未开始';
        elseif (time() > strtotime($this->data['delist_time']))
            $status = '已抢光';
        elseif ($risen_price)
            $status = $risen_price === $original_price ? '折扣结束' : '已涨价';
        else
            $status = '去抢购';

        if ($status != '已涨价') $risen_price = null;
        return array($discount_price, $vip, $original_price, $risen_price, $status);
    }


    function get_price()
    {
        $now_price = $price = $this->data['price'];
        $vip = false;
        if (($promo_price = $this->data['promo_price']) && $promo_price < $now_price)
            $now_price = $promo_price;
        if (($vip_price = $this->data['vip_price']) && $vip_price < $now_price)
        {
            $now_price = $vip_price;
            $vip = true;
        }
        $ref_price = $this->data['ref_price'];
        if ($now_price <= $ref_price * 1.2)
        {
            $discount_price = $now_price;
            $risen_price    = null;
        }
        else 
        {
            $discount_price = $ref_price;
            $risen_price    = $now_price;
        }
        return array($discount_price, $vip, $price, $risen_price);
    }

    function jump_url()
    {
        if ($click_url = $this->data['click_url']) return $click_url;
        $detail_url = urlencode($this->data['detail_url']);
        return "http://s.click.taobao.com/t_9?l={$detail_url}&pid=mm_40339139_0_0"; #&unid=206481310
    }
}
