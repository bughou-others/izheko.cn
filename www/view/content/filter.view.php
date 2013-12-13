            <div id="condition">
                <span id="filter">
<?php
foreach(array(
    ''         => '全部折扣',
    '9kuai9'   => '9块9包邮',
    '20yuan'   => '20元封顶',
) as $f => $name)
{
    if ($f === '') $class = 'lrc';
    else if ($f === '20yuan') $class = 'rrc';
    else $class = '';

    if($f == $filter) {
        $count = $data['total_count'];
        echo "<span class=\"$class\">$name ($count)</span>";
    } else {
        $count = $data[$f . '_count'];
        echo "<a href=\"$type_url$f\" class=\"$class\">$name ($count)</a>";
    }
}
?>
                </span>
                <span id="sort">
<?php if ($sort === 'newest') { ?>
                    <a class="lrc" title="最热排序" href="<?= $filter_url ?>">最热</a><span class="rrc" title="最新排序">最新</span>
<?php } else { ?>
                    <span class="lrc" title="最热排序">最热</span><a class="rrc"  title="最新排序" href="<?= $filter_url ?>newest">最新</a>
<?php } ?>
                </span>
            </div>
