<?php
require 'number.helper.php';

foreach(array(
    '' => NULL, '非' => null, 'a' => null, '三十非' => null, '三八' => null,
    '九' => 9, '五十九' => 59, '三百八十九' => 389, '一千八百三十七' => 1837,
    '十' => 10, '十三' => 13,
    '百零三'    => 103,   '百三'    => 130,  '百三十'   => 130,  '百零三十'   => 130,
    '一百零三'  => 103,   '一百三'  => 130,  '一百三十' => 130,  '一百零三十' => 130, 
    '一千零三'  => 1003,  '一千三'  => 1300, '一千三百' => 1300, '一千零三百' => 1300,
    '一千零三十'=> 1030,  '一千三十'=> 1030,
) as $input => $expect)
{
    $output = Number::from_chinese($input);
    if ($output !== $expect) {
        echo 'input: ';
        var_dump($input);
        echo 'expect: ';
        var_dump($expect);
        echo 'output: ';
        var_dump($output);
        echo PHP_EOL;
    }
}




