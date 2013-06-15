<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="/static/main.css" />
    </head>
    <body>
        <div class="header">
            <a href="/"><img src="/static/logo.png" alt="爱折扣" /></a>
            <form class="search">
                <div class="input_wrapper"><input type="text" name="s" /></div>
                <div class="submit_wrapper"><button type="submit">搜 索</button></div>
            </form>
        </div>
        <div class="nav">
            <a href="/"<?= $type ? '' : ' class="on"' ?>>全部</a>
<?php
foreach($types as $one) {
    list($name, $pinyin, $count) = $one;
    $class = $pinyin === $type ? ' class="on"' : '';
    echo <<<EOL
            <a href="/$pinyin"$class>$name</a>\n
EOL;
}
?>
            <div class="clearfix"></div>
        </div>
        <div class="content">
<?php
require_once APP_ROOT . '/../common/helper/price.helper.php';
foreach($items as $item) { 
    list($discount_price, $vip, $original_price, $risen_price, $status) = $item->get_price_and_status();
    if ($risen_price) $risen_price = format_price($risen_price);
    $status_data = array(
        '未开始'   => array( 'green', '折扣活动还没开始哟。'),
        '去抢购'   => array( 'red',   '快去抢购吧！'),
        '已涨价'   => array( 'gray2', "宝贝已经涨价为 ￥$risen_price 啦。"),
        '折扣结束' => array( 'gray',  '折扣活动已经结束啦。'),
        '已抢光'   => array( 'gray',  '宝贝被抢光，已经下架啦。'),
    );
    list($style, $title) = $status_data[$status];
    list($discount_price_yuan, $discount_price_fen) = split_price($discount_price);
?>
            <div class="item">
                <div class="title">
                    <b><?= $item->get_type_tag() ?></b>
                    <a target="_blank" href="<?= $item->jump_url() ?>">
                        <?= $item->get_title() ?>
                    </a>
                </div>
                <a target="_blank" href="<?= $item->jump_url() ?>">
                    <img src="<?= $item->get_pic_url() ?>" />
                </a>
                <div class="buy">
                    <a class="action <?= $style ?>" title="<?= $title ?>" target="_blank" href="<?= $item->jump_url() ?>">
                        <?= $status ?>
                        <?= $risen_price ? "<div>￥$risen_price</div>" : null ?>
                    </a>
                    <span title="折扣价 ￥<?= format_price($discount_price) ?>">￥<big><?=
                            $discount_price_yuan ?></big>.<?= $discount_price_fen ?></span>
                    <?php if ($original_price > $discount_price) { ?>
                    <small title="原价 ￥<?= format_price($original_price) ?>">￥<?= format_price($original_price) ?></small>
                    <?php } ?>
                </div>
                <div class="flags">
                    <?= $item->postage_free() ? '<span class="post">包邮</span> ' : null ?>
                    <?= $vip ? '<span class="vip" title="淘宝VIP用户价哟。">VIP价</span>' : null ?>
                </div>
            </div>
<?php } ?>
        </div>
        <div class="page">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    echo paginate($type ? "/$type/" : '/', '.html', $page, $total_count, $page_size);
?>
        </div>
        <div class="footer">
            <p>爱折扣 © 2013 izheko.cn</p>
        </div>
    </body>
</html>
