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

    static function time_cond($cond)
    {
        return " and list_time $cond and (
            promo_start is null or promo_price > vip_price or promo_start $cond
        )";
    }


    static function query($type, $word, $filter, $page, $page_size)
    {
        if($type && $type !== 'all') {
            $type_id = DB::get_value("select id from types where pinyin = '$type'");
            if(!$type_id) return;
            $condition = "and type_id=$type_id";
        }
        else $condition = '';

        if(strlen($word) > 0) {
            $word = DB::escape($word);
            $condition .= " and title like '%$word%'";
        } 

        $now      = strftime('%F %T');
        $today    = strftime('%F %T', strtotime('today'));
        $tomorrow = strftime('%F %T', strtotime('tomorrow'));
        $new_cond      = $condition . self::time_cond("between '$today' and '$tomorrow'");
        $coming_cond   = $condition . self::time_cond("between '$now'   and '$tomorrow'");
        $tomorrow_cond = $condition . self::time_cond(">= '$tomorrow'");
        $default_cond  = $condition . (strlen($word) > 0 ? '' : self::time_cond("< '$tomorrow'"));

        $data = array();
        self::filter($data, $filter, 'new',      $new_cond,      $page, $page_size);
        self::filter($data, $filter, 'coming',   $coming_cond,   $page, $page_size);
        self::filter($data, $filter, 'tomorrow', $tomorrow_cond, $page, $page_size);
        self::filter($data, $filter, null,       $default_cond,  $page, $page_size);

        return $data;
    }

    static function filter(&$data, $filter, $target, $condition, $page, $page_size)
    {
        if($filter === $target) {
            self::select($data, $condition, $page, $page_size);
        } elseif ($target !== null) {
            $data[$target . '_count'] = self::count($condition);
        }
    }

    static function count($condition)
    {
        $sql = 'select count(*) from items
            where title != "" and !(flags&' . ItemBase::FLAGS_MASK_ITEM_DELETED . ')' .
            $condition;
        return DB::get_value($sql);
    }

    static function select(&$data, $condition, $page, $page_size)
    {
        $offset = $page >= 1 ? ($page - 1) * $page_size : 0;
        $sql = "select SQL_CALC_FOUND_ROWS 
            title,type_id,flags,ref_price,price,promo_price,vip_price,
            promo_start,promo_end,list_time,delist_time,detail_url,click_url,pic_url
            from items
            where title != '' and !(flags&" . ItemBase::FLAGS_MASK_ITEM_DELETED . ")
            $condition 
            order by id desc limit $offset, $page_size";
        $result = DB::query($sql);
        $instances = array();
        while($row = $result->fetch_assoc()) $instances[] = new self($row);
        $data['items']       = &$instances;
        $data['total_count'] = DB::get_value('select found_rows()');
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

    function now_price()
    {
        $now_price = $this->data['price'];
        $this->vip   = false;
        $this->promo = false;
        if (($promo_price = $this->data['promo_price']) && $promo_price < $now_price)
        {
            $now_price   = $promo_price;
            $this->promo = true;
        }
        if (($vip_price = $this->data['vip_price']) && $vip_price < $now_price)
        {
            $now_price = $vip_price;
            $this->vip = true;
        }
        return $now_price;
    }

    function discount_price()
    {
        if (isset($this->discount_price)) return $this->discount_price;
        $now_price = $this->now_price();
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

    function start_time()
    {
        $time = strtotime($this->data['list_time']);
        isset($this->promo) || $this->now_price();
        if ($this->promo && ($t = strtotime($this->data['promo_start'])) > $time)
            $time = $t;
        return $time;
    }

    function action()
    {
        if (isset($this->action)) return $this->action;
        $now = time();
        if ($now < ($start_time = $this->start_time()))
        {
            $start_time = strftime('%H:%M', $start_time);
            $this->action        = $start_time;
            $this->action_style  = 'green';
            $this->action_title  = "折扣 $start_time 开始哟";
        }
        elseif ($now > strtotime($this->data['delist_time']))
        {
            $this->action        = '已抢光';
            $this->action_style  = 'gray';
            $this->action_title  = '宝贝被抢光，已经下架啦。';
        }
        elseif (($risen_price = $this->risen_price()) || $this->promo && $now > strtotime($this->data['promo_end']))
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
            $this->action = '去抢购';
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
        isset($this->vip) || $this->now_price();
        if($this->vip) return '<span class="vip" title="淘宝VIP用户价哟。">VIP价</span>';
    }

    function jump_url()
    {
        if ($click_url = $this->data['click_url']) return $click_url;
        $detail_url = urlencode($this->data['detail_url']);
        return "http://s.click.taobao.com/t_9?l={$detail_url}&pid=mm_40339139_0_0"; #&unid=206481310
    }
}
