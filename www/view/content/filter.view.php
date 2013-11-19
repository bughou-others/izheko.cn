            <div id="filter">
<?php
foreach(array(
    ''         => '全部折扣',
    '9kuai9'   => '9块9包邮',
    '20yuan'   => '20元封顶',
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
?>
                <a href="<?= "$page_url$target\"$class>$name ($count)" ?></a>
<?php
}
?>
            </div>
