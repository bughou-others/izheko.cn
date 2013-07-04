        <div id="filter">
<?php
foreach(array(
    'new'      => '今日新品',
    'coming'   => '即将开始',
    'tomorrow' => '明日预告'
) as $target => $name)
{
    if($target == $filter)
    {
        $class = ' class="on"';
        $count = $data['total_count'];
    }
    else
    {
        $class = '';
        $count = $data[$target . '_count'];
    }
    echo "<a href=\"$page_url$target\"$class>$name<span>($count)</span></a>\n";
}
?>
        </div>
