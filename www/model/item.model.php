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
        return "<a class=\"type\" href=\"/$pinyin\">【{$name}】</a>";
    }

    function title()
    {
        return preg_replace('/^【[^】]*】/', '', $this->data['title']);
    }

    function pic_url($empty_ref = false)
    {
        if ($this->data['flags'] & self::FLAGS_MASK_NO_REF_PIC) {
            #170 190 210 240 270 290 300 310 320 350 360 400 430
            return $this->data['pic_url'] . '_290x290.jpg';
        }
        if ($empty_ref) return;
        return 'http://static.izheko.cn/' . self::pic_path($this->data['num_iid']);
    }
    
    function end_time()
    {
        return strftime('%m月%d日%H:%M', strtotime($this->data['end_time']));
    }

    function time_left()
    {
        $now = time();
        $start_time = strtotime($this->data['start_time']);
        if ($now < $start_time)
            return "折扣即将开始：<span s=\"$start_time\"></span>";

        $end_time = strtotime($this->data['end_time']);
        if ($now < $end_time) {
            $ref_end_time = strtotime($this->data['ref_end_time']);
            if ($now < $ref_end_time && $ref_end_time < $end_time) $end_time = $ref_end_time;
            return "折扣剩余时间：<span s=\"$end_time\"></span>";
        }

        return '折扣结束时间：' . strftime('%m月%d日%H:%M', $end_time);
    }


    function discount_price_str()
    {
        if (! isset($this->discount_price_str)) 
            $this->discount_price_str = format_price($this->data['ref_price']);
        return $this->discount_price_str;
    }

    function discount_price_yuan()
    {
        if (! isset($this->discount_price_yuan))
            list($this->discount_price_yuan, $this->discount_price_fen) = split_price($this->data['ref_price']);
        return $this->discount_price_yuan;
    }

    function discount_price_fen()
    {
        if (! isset($this->discount_price_fen))
            list($this->discount_price_yuan, $this->discount_price_fen) = split_price($this->data['ref_price']);
        return $this->discount_price_fen;
    }

    function original_price_str()
    {
        if (! isset($this->original_price_str)) {
            if (($price = $this->data['price']) > $this->data['ref_price']){
                $this->original_price_str = floor($price / 100);
            }
            else $this->original_price_str = false;
        }
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
            $this->action_style  = 'wks';
            $tomorrow = strtotime('tomorrow');
            if($start_time < $tomorrow) $time = strftime('今天%H:%M', $start_time);
            elseif($start_time < $tomorrow + 86400) $time = strftime('明天%H:%M', $start_time);
            else $time = strftime('%F %H:%M', $start_time);
            $this->action_title  = "折扣 $time 开始哟";
        }
        elseif ($now > strtotime($this->data['delist_time']))
        {
            $this->action        = '';
            $this->action_style  = 'yqg';
            $this->action_title  = '宝贝被抢光，已经下架啦。';
        }
        elseif ($now > strtotime($this->data['end_time']))
        {
            $this->action        = '';
            $this->action_style  = 'yjs';
            $this->action_title  = '折扣已经结束啦。';
        }
        else
        {
            $this->action        = '';
            $this->action_style  = 'qqg';
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
            return '<b class="post" title="卖家包邮"></b>';
    }

    function vip_tag()
    {
        if($this->data['flags'] & self::FLAGS_MASK_VIP_PRICE)
            return '<b class="vip" title="淘宝VIP用户价"></b>';
    }

    function paigai_tag()
    {
        if($this->data['ref_price'] < $this->data['now_price'])
            return '<b class="gai" title="拍下自动改价"></b>';
    }

    function jump_url()
    {
        return $this->data['detail_url'];
        if ($click_url = $this->data['click_url']) return $click_url;
        $detail_url = urlencode($this->data['detail_url']);
        return "http://s.click.taobao.com/t_9?l={$detail_url}&pid=mm_40339139_0_0"; #&unid=206481310
    }
}
