<?php
function paginate($prefix, $suffix, $current, $total, $size, $left = 3, $middle = 5, $right = 3)
{
    $current = (int)$current;
    $last = ceil($total / $size);
    if($last < 1 || $current > $last) return;
    if($current < 1) $current = 1;

    $nav = '';
    if(($prev = $current - 1) >= 1) $nav .= <<<EOT
<a href="$prefix$prev$suffix" class="on">上一页</a>\n
EOT;
    /* 
     * divide all page number into three parts: left, middle, right。
     * calculate the left end and right start page number。
     */
    $left_end  = $current - ceil($middle / 2);
    $left_end2 = $last - $right - $middle;
    if($left_end2 < $left_end) $left_end = $left_end2;
    if($left_end < $left) $left_end = $left;
    $right_start = $last - $right + 1;

    $nav_count = $left + $middle + $right;
    if($last < $nav_count) $nav_count = $last;
    $left_last = $left;
    $right_first = $left + $middle + 1;
    for($i = $now = 1; $i <= $nav_count; $i++)
    {
        if($i == $left_last && $now < $left_end)
        {
            $nav .= "<span>...</span>\n";
            $now  = $left_end + 1;
        }
        elseif($i == $right_first && $now < $right_start)
        {
            $nav .= "<span>...</span>\n";
            $now  = $right_start + 1;
        }
        else 
        {
            if($now == $current)
                $nav .= "<span class=\"on\">$now</span>\n";
            else $nav .= <<<EOT
<a href="$prefix$now$suffix">$now</a>\n
EOT;
            $now++;
        }
    }

    if(($next = $current + 1) <= $last) $nav .= <<<EOT
            <a href="$prefix$next$suffix" class="on">下一页</a>\n
EOT;
    return $nav;
}
