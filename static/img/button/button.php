<?php
if ($argc !== 2 || !preg_match('/.+\.png$/', $argv[1]))
    die("usage: php ${argv[0]} <*.png>\n");

button_img($argv[1]);

function button_img($file)
{
    $img = new Imagick();
    $img->newImage(192, 154, 'transparent', 'png');

    $draw = new ImagickDraw();
    $draw->setTextAlignment(Imagick::ALIGN_CENTER);

    $draw->setFont('./msyhbd.ttf');
    $draw->setFontSize(16); //in pixel? doc say point.
    $draw->setFontWeight(700);

    $x = 0; $y = 0;
    draw_button($draw, $x, $y, 80, 33, 5, '#e33',    '去抢购', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#808080', '已结束', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#808080', '已抢光', 24, 'v');
    draw_button($draw, $x, $y, 80, 33, 5, '#393',     null,    24, 'hv');
    $x1 = $x; $y1 = $y;

    $y = 0;
    draw_circle($draw, $x, $y, 55, '#cc0000', "频道", 'h');
    draw_circle($draw, $x, $y, 55, '#cc0000', "收藏", 'v');
    $x = $x1;
    draw_circle($draw, $x, $y, 55, '#cc0000', '反馈', 'h');
    draw_circle($draw, $x, $y, 55, '#aaa',    '顶部');

    $draw->setFont('./msyh.ttf');
    $draw->setFontSize(12);
    $draw->setFontWeight(500);

    $x = 0; $y = $y1;
    draw_button($draw, $x, $y, 26, 17, 3, '#393',    '包邮',     13, 'h');
    draw_button($draw, $x, $y, 26, 17, 3, '#f39',    '拍改',     13, 'h');
    draw_button($draw, $x, $y, 34, 17, 3, '#ffa405', 'VIP价',    13, 'h');
    draw_button($draw, $x, $y, 54, 17, 3, '#e33',    '相关热卖', 13, 'h');

    
    $img->drawImage($draw);
    $img->writeImage($file);
}

function draw_button($draw, &$x, &$y, $width, $height, $br, $bg, $text, $baseline, $flag = null)
{
    $draw->setFillColor($bg);
    $draw->roundRectangle($x, $y, $x + $width - 1, $y + $height - 1, $br, $br);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation($x + ($width + 1) / 2, $y + $baseline, $text);
    }
    if ($flag === null) return;
    else if ($flag === 'h')    $x += $width  + 1;
    else if ($flag === 'v')    $y += $height + 1;
    else if ($flag === 'hv') { $x += $width  + 1; $y += $height + 1; }
}

function draw_circle($draw, &$x, &$y, $diameter, $bg, $text, $flag = null)
{
    $draw->setFillColor($bg);
    $radius = floor($diameter / 2);
    $draw->circle($x + $radius, $y + $radius, $x, $y + $radius);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation($x + ($diameter + 1) / 2, $y + $radius, $text);
    }
    if ($flag === null) return;
    else if ($flag === 'h')    $x += $diameter + 1;
    else if ($flag === 'v')    $y += $diameter + 1;
    else if ($flag === 'hv') { $x += $diameter + 1; $y += $diameter + 1; }
}

