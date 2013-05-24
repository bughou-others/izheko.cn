<?php
function split_price($price)
{
    $price = (int)$price;
    if     ($price < 10)  return array('0', '0' .   $price);
    elseif ($price < 100) return array('0', (string)$price);
    else 
    {
        $price = (string)$price;
        return array(
            substr($price, 0, strlen($price) - 2),
            substr($price, -2),
        );
    }
}

function format_price($price)
{
    $price = (int)$price;
    if     ($price <  10) return '0.0' . $price;
    elseif ($price < 100) return '0.'  . $price;
    else return substr_replace($price, '.', -2, 0);
}

function parse_price($price)
{
    if (! $price) return 0;
    $price = explode('.', $price, 2);
    if (isset($price[1]) && ($fen = $price[1]))
    {
        switch (strlen($fen))
        {
        case 1:
            $fen = (int)$fen * 10;
        case 2:
            $fen = (int)$fen;
        default:
            $fen = (int)substr($fen, 0, 2);
        }
    }
    else $fen = 0;
    return $price[0] * 100 + $fen;
}

