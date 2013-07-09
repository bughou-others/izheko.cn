<?php
require_once APP_ROOT . '/../common/model/item_base.model.php';
require_once APP_ROOT . '/../common/helper/price.helper.php';
require_once APP_ROOT . '/model/type.model.php';

class Item extends ItemBase
{
    static function types()
    {
        $sql = "select type_id, count(*) count from items where title != '' and type_id > 0
            group by type_id";
        $result = DB::query($sql);
        $types = array();
        $all = Type::all();
        while(list($id, $count) = $result->fetch_row())
        {
            list($name, $pinyin) = $all[$id];
            $types[$id] = array($name, $pinyin, $count);
        }
        return $types;
    }

    function __construct($data)
    {
        $this->data = $data;
    }
    
    function get($name)
    {
        if (isset($this->data[$name])) return $this->data[$name];
    }

    function type_tag()
    {
        static $types;
        if(!$types) $types = Type::all();
        $type_id = $this->data['type_id'];
        if(!isset($types[$type_id])) return;
        list($name, $pinyin) = $types[$type_id];
        return "<a href=\"/$pinyin\">【{$name}】</a>";
    }

    function title()
    {
        return preg_replace('/^【[^】]*】/', '', $this->data['title']);
    }

    function pic_url()
    {
        #170 190 210 240
        return $this->data['pic_url'] . '_210x210.jpg';
    }

    function discount_price()
    {
        if (isset($this->discount_price)) return $this->discount_price;
        $now_price = $this->data['now_price'];
        $ref_price = $this->data['ref_price'];
        if ($ref_price <= 0 || $now_price <= $ref_price * self::factor_price_risen)
        {
            $this->discount_price = $now_price;
            $this->risen_price    = null;
        }
        else
        {
            $this->discount_price = $ref_price;
            $this->risen_price    = $now_price;
        }
        return $this->discount_price;
    }

    function discount_price_str()
    {
        if (! isset($this->discount_price_str)) 
            $this->discount_price_str = format_price($this->discount_price());
        return $this->discount_price_str;
    }

    function discount_price_yuan()
    {
        if (! isset($this->discount_price_yuan))
            list($this->discount_price_yuan, $this->discount_price_fen) = split_price($this->discount_price());
        return $this->discount_price_yuan;
    }

    function discount_price_fen()
    {
        if (! isset($this->discount_price_fen))
            list($this->discount_price_yuan, $this->discount_price_fen) = split_price($this->discount_price());
        return $this->discount_price_fen;
    }

    function risen_price()
    {
        isset($this->risen_price) || $this->discount_price();
        return $this->risen_price;
    }

    function original_price_str()
    {
        if (! isset($this->original_price_str)) $this->original_price_str = 
            ($price = $this->data['price']) > $this->discount_price() ? format_price($price) : null;
        return $this->original_price_str;
    }

    function action()
    {
        if (isset($this->action)) return $this->action;
        $now = time();
        $start_time = strtotime($this->data['start_time']);
        if ($now < $start_time)
        {
            $time = strftime('%H:%M', $start_time);
            $this->action        = $time;
            $this->action_style  = 'green';
            $tomorrow = strtotime('tomorrow');
            if($start_time < $tomorrow) $time = strftime('今天%H:%M', $start_time);
            elseif($start_time < $tomorrow + 86400) $time = strftime('明天%H:%M', $start_time);
            else $time = strftime('%F %H:%M', $start_time);
            $this->action_title  = "折扣 $time 开始哟";
        }
        elseif ($now > strtotime($this->data['delist_time']))
        {
            $this->action        = '已抢光';
            $this->action_style  = 'gray';
            $this->action_title  = '宝贝被抢光，已经下架啦。';
        }
        elseif (($risen_price = $this->risen_price()) || $now > strtotime($this->data['end_time']))
        {
            if ($risen_price === null || $risen_price === $this->data['price']) {
                $this->action        = '已结束';
                $this->action_style  = 'gray';
                $this->action_title  = '折扣已经结束啦。';
            } else {
                $risen_price         = format_price($risen_price);
                $this->action        = "￥$risen_price";
                $this->action_style  = 'gray';
                $this->action_title  = "宝贝已经涨价为 ￥$risen_price 啦。";
            }
        }
        else
        {
            $this->action        = '去抢购';
            $this->action_style  = 'yellow';
            $this->action_title  = '折扣正在进行，快去抢购吧！';
        }
        return $this->action;
    }

    function action_style()
    {
        isset($this->action_style) || $this->action();
        return $this->action_style;
    }

    function action_title()
    {
        isset($this->action_title) || $this->action();
        return $this->action_title;
    }

    function postage_tag()
    {
        if($this->data['flags'] & self::FLAGS_MASK_POSTAGE_FREE)
            return '<span class="post">包邮</span> ';
    }

    function vip_tag()
    {
        if($this->data['flags'] & self::FLAGS_MASK_VIP_PRICE)
            return '<span class="vip" title="淘宝VIP用户价哟。">VIP价</span>';
    }

    function jump_url()
    {
        return $this->data['detail_url'];
        if ($click_url = $this->data['click_url']) return $click_url;
        $detail_url = urlencode($this->data['detail_url']);
        return "http://s.click.taobao.com/t_9?l={$detail_url}&pid=mm_40339139_0_0"; #&unid=206481310
    }
}
