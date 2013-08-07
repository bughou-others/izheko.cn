<?php
$img = new Imagick();
$img->newImage(128, 60, '#010101', 'png');

$draw = new ImagickDraw();
$draw->setFont('./pangwa.ttf');
$draw->setTextAlignment(Imagick::ALIGN_CENTER);
$draw->setFillColor('#84d516'); 
//$draw->setTextAntialias(false);

$draw->setFontSize(40);
$img->annotateImage($draw, 64, 35, 0, '爱折扣');

$draw->setFontSize(18);
$draw->setTextKerning(2);
$img->annotateImage($draw, 64, 57, 0, 'izheko.cn');

$img->writeImage($argv[1]);

function logo_by_gd()
{
    $img = imagecreate(128, 60);
    imagecolorallocate($img, 0xff, 0xff, 0xff);

    $c = imagecolorallocate($img, 0xff, 0, 0);
    imagettftext($img, 40 * 0.75, 0, 0, 40, $c, './pangwa.ttf', '爱折扣');

    imagepng($img, $_SERVER['argv'][1]);
}
