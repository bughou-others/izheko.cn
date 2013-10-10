<?php
if ($argc !== 2 || !preg_match('/.+\.png$/', $argv[1]))
    die("usage: php ${argv[0]} <*.png>\n");

button_img($argv[1]);

function button_img($file)
{
    $img = new Imagick();
    $img->newImage(80, 172, '#fff', 'png');

    $draw = new ImagickDraw();
    //$draw->setStrokeWidth(0);
    $draw->setFont('./msyhbd.ttf');
    //in pixel ? doc say point
    $draw->setFontSize(16);
    $draw->setFontWeight(700);
    $draw->setTextAlignment(Imagick::ALIGN_CENTER);

    $y = 0;
    draw_button($draw, $y, '#e33',    '去抢购');
    draw_button($draw, $y, '#808080', '已结束');
    draw_button($draw, $y, '#808080', '已抢光');
    draw_button($draw, $y, '#393',     null);

    $draw->setFont('./msyh.ttf');
    $draw->setFontSize(12);
    $draw->setFontWeight(500);

    $x = 0;
    draw_button2($draw, $x, $y, 26, '#393', '包邮');
    draw_button2($draw, $x, $y, 34, '#ffa405', 'VIP价');
    $x = 0;
    $y += 18;
    draw_button2($draw, $x, $y, 26, '#f39', '拍改');
    
    $img->drawImage($draw);
    $img->writeImage($file);
}

function draw_button($draw, &$y, $bg, $text)
{
    $draw->setFillColor($bg);
    $draw->roundRectangle(0, $y, 80, $y += 32, 5, 5);
    if ($text) {
        $draw->setFillColor('#fff');
        $draw->annotation(40, $y - 8, $text);
    }
    $y += 2;
}

function draw_button2($draw, &$x, $y, $width, $bg, $text)
{
    $draw->setFillColor($bg);
    $draw->roundRectangle($x, $y, $x + $width - 1, $y + 16, 3, 3);
    $draw->setFillColor('#fff');
    $draw->annotation($x + $width / 2, $y + 13, $text);
    $x += $width + 1;
}

