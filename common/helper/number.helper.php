<?php
class Number
{
    static function parse($str, &$number)
    {
        if (preg_match('/^[0-9]+$/', $str)) $number = $str;
        else {
            $number = self::from_chinese($str);
            if ($number === NULL) echo 'unknow chinese number: ' . $str;
        }
        return is_int($number) || is_string($number) && preg_match('/^\d+$/', $number);
    }


    static function from_chinese($str)
    {
        $number = array(
            '一' => 1, '二' => 2, '三' => 3, '四' => 4, 
            '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9
        );
        $units = array(array('十', 10), array('百', 100), array('千', 1000));

        $value = null;
        $char_array = preg_split('//u', $str);
        $count = count($char_array) - 1;
        for ($i = 1; $i < $count; $i++) {
            $char = $char_array[$i];
            if ($char === '零') continue;
            elseif (isset($number[$char])) {
                $unit = $char_array[++$i];
                if ($unit === '') {
                    if ($value === null || empty($units) || $char_array[$i - 2] == '零')
                        $value += $number[$char];
                    else $value += $number[$char] * $units[count($units) - 1][1];
                } elseif ($unit_value = self::search_unit($units, $unit)) {
                    $value += $number[$char] * $unit_value;
                } else return null;
            } elseif (($unit_value = self::search_unit($units, $char)) && $value === null) {
                $value = $unit_value;
            } else return null;
        }
        return $value;
    }

    static function search_unit(&$units, $target) {
        foreach($units as $i => $one) {
            if ($one[0] === $target) {
                array_splice($units, $i);
                return $one[1];
            }
        }
    }
    //$unit2 = array( '万' => 10000, '亿' => 10000 * 10000 );
}
