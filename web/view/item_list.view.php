<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <link charset="utf-8" rel="stylesheet" type="text/css" href="/static/main.css" />
</head>
<body>
  <div id="header">
    <a href="/"><img src="/static/izheko-header.png" alt="爱折扣" /></a>
    <div id="nav">
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
  </div>
  <div id="main">
<?php
require_once APP_ROOT . '/../common/helper/price.helper.php';
foreach($items as $item) { 
    list($discount_price, $vip, $original_price, $risen_price, $status) = $item->get_price_and_status();
    if ($risen_price) $risen_price = format_price($risen_price);
    $status_data = array(
        '未开始'   => array( 'green', '折扣活动还没开始哟。'),
        '去抢购'   => array( 'red',   '快去抢购吧！'),
        '已涨价'   => array( 'gray',  "宝贝已经涨价为 ￥$risen_price 啦。"),
        '折扣结束' => array( 'gray',  '折扣活动已经结束啦。'),
        '已抢光'   => array( 'gray',  '宝贝被抢光，已经下架啦。'),
    );
    list($style, $title) = $status_data[$status];
    list($discount_price_yuan, $discount_price_fen) = split_price($discount_price);
?>
    <div class="item">
      <a target="_blank" href="<?= $item->jump_url() ?>">
        <img src="<?= $item->get_pic_url() ?>" />
        <h3><?= $item->postage_free() ? '<b>包邮</b> ' : null ?><span><?= $item->get_title() ?></span></h3>
        <div class="<?= $style ?>" title="<?= $title ?>"><?= $status ?><?=
          $risen_price ? "<div>￥$risen_price</div>" : null
        ?></div>
      </a>
      <div>
        <span title="折扣价<?= format_price($discount_price) ?>">￥<big><?=
             $discount_price_yuan ?></big>.<?= $discount_price_fen ?></span>
        <?= $vip ? '<sup title="这是淘宝VIP用户价哟。">VIP价</sup>' : null ?>
        <?php if ($original_price > $discount_price) { ?>
        <small title="原价<?= format_price($original_price) ?>"><?= format_price($original_price) ?></small>
        <?php } ?>
      </div>
    </div>
<?php } ?>
  </div>
  <div class="pagination">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    echo paginate($type ? "/$type/" : '/', '.html', $page, $total_count, $page_size);
?>
  </div>
</body>
</html>
