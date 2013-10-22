<?php
if ($argc < 2 || $argc > 3 ||
    !preg_match('/.+\.(png|ico)$/', $argv[1]) ||
    $argc === 3 && $argv[2] !== 'green' && $argv[2] !== 'white' && $argv[2] !== 'gray'
) die("usage: php ${argv[0]} <*.{png | ico}> [green | white | gray] \n");

$func = preg_match('/.*\.png$/', $argv[1]) ? 'logo' : 'favicon';
    
if ($argc == 3 && $argv[2] === 'gray')
    $func($argv[1], '#f0f0f0', '#c4c4c4', 8, 65);
elseif ($argc == 3 && $argv[2] === 'white')
    $func($argv[1], '#444',    '#fff'   );
else
    $func($argv[1], '#010101', '#84d516');

function logo($file, $background_color, $foreground_color, $padding_x = 0, $padding_y = 0)
{
    $img = new Imagick();
    $img->newImage(290, 60 + 2 * $padding_y, $background_color, 'png');

    $draw = new ImagickDraw();
    $draw->setFont('./pangwa.ttf');
    $draw->setTextAlignment(Imagick::ALIGN_CENTER);
    $draw->setFillColor($foreground_color); 
    //$draw->setTextAntialias(false);

    $draw->setFontSize(40);
    $img->annotateImage($draw, 65 + $padding_x, 35 + $padding_y, 0, '爱折扣');

    $draw->setFontSize(18);
    $draw->setTextKerning(2);
    $img->annotateImage($draw, 65 + $padding_x, 57 + $padding_y, 0, 'izheko.cn');

    $draw->setFont('../button/msyhbd.ttf');
    $img->annotateImage($draw, 215 - $padding_x, 40 + $padding_y, 0, '九块九，天天有');
    $img->writeImage($file);
}

function favicon($file, $background_color, $foreground_color)
{
    $img = new Imagick();
    $img->newImage(16, 16, $background_color, 'ico');

    $draw = new ImagickDraw();
    $draw->setFont('./pangwa.ttf');
    $draw->setTextAlignment(Imagick::ALIGN_LEFT);
    $draw->setFillColor($foreground_color); 

    $draw->setFontSize(14);
    $img->annotateImage($draw, 1, 13, 0, '折');

    $img->writeImage($file);
}

function logo_by_gd($file)
{
    $img = imagecreate(128, 60);
    imagecolorallocate($img, 0xff, 0xff, 0xff);

    $c = imagecolorallocate($img, 0xff, 0, 0);
    imagettftext($img, 40 * 0.75, 0, 0, 40, $c, './pangwa.ttf', '爱折扣');

    imagepng($img, $file);
}
