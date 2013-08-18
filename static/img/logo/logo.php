<?php
if ($argc < 2 || $argc > 3 ||
    !preg_match('/.+\.(png|ico)$/', $argv[1]) ||
    $argc === 3 && $argv[2] !== 'green' && $argv[2] !== 'white' && $argv[2] !== 'red'
) die("usage: php ${argv[0]} <*.{png | ico}> [green | white | red] \n");

$func = preg_match('/.*\.png$/', $argv[1]) ? 'logo' : 'favicon';
    
if ($argc == 3 && $argv[2] === 'red')
    $func($argv[1], '#fff',    '#f40'   );
elseif ($argc == 3 && $argv[2] === 'white')
    $func($argv[1], '#444',    '#fff'   );
else
    $func($argv[1], '#010101', '#84d516');

function logo($file, $background_color, $foreground_color)
{
    $img = new Imagick();
    $img->newImage(128, 60, $background_color, 'png');

    $draw = new ImagickDraw();
    $draw->setFont('./pangwa.ttf');
    $draw->setTextAlignment(Imagick::ALIGN_CENTER);
    $draw->setFillColor($foreground_color); 
    //$draw->setTextAntialias(false);

    $draw->setFontSize(40);
    $img->annotateImage($draw, 64, 35, 0, '爱折扣');

    $draw->setFontSize(18);
    $draw->setTextKerning(2);
    $img->annotateImage($draw, 64, 57, 0, 'izheko.cn');

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
