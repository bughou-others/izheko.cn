<?php
require_once APP_ROOT . '/../common/helper/number.helper.php';

class ChangePrice
{
    static function parse($str)
    {
        //var_dump($str);
        $price = self::parse1($str);
        if($price === null) $price = self::parse2($str);
        if($price) return array(
            'price'      => $price,
            'price_type' => '拍下改价'
        );
    }

    static function parse1($str)
    {
        $change_price = '(?:(?:价格)?修?[改变]价?[成为至到]?|(?:价格)?[减降]价?[成为至到]|只要)';
        $format_array = array(
            "拍下?后?就?立?$change_price?%s",
            "自动$change_price?%s", '^[促改]?%s$',
            '(?:秒杀价?|惊呆价|实付|特惠|价格为)%s',
            '%s(?:.*包邮|秒杀)',
        );
        return self::do_parse($format_array, $str);
    }

    static function parse2($str)
    {
        $change_price = '(?:[减降]价?)';
        $format_array = array(
            "拍下?后?就?立?$change_price?%s",
            "自动$change_price?%s",
        );
        $price = self::do_parse($format_array, $str);
        if($price) return -$price;
    }

    static function do_parse($format_array, $str)
    {
        foreach($format_array as $format)
        {
            $re = '/' . sprintf($format,
                ' *￥?([0-9一二三四五六七八九十]{1,3})[元块点]([0-9零一二三四五六七八九]{1,2})?元?')
                . '/u';
            if (preg_match($re, $str, $m))
            {
                if (Number::parse($m[1], $yuan) && 
                    (!isset($m[2]) || Number::parse($m[2], $fen))
                )
                return $yuan . '.' . (isset($fen) ? $fen : '0');
            }
            else
            {
                $re = '/' . sprintf($format, ' *￥?([0-9]{1,3}(\.[0-9]{1,2})?)[元块]?') . '/u';
                if (preg_match($re, $str, $m)) return $m[1];
            }
        }
    }
}


