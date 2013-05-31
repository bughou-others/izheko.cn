<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      text-align: center;
      font-family: "微软雅黑";
      background-color: white;
    }

    #main {
      margin-top: 10px;
    }
    .item {
      display:inline-block;
      *display:inline;
      *zoom:1;
      vertical-align: top; 
      width: 240px;
      margin: 10px;
      border: 1px solid #ccc;
      text-align: left;
      color: black;
    }
    .item > a {
      text-decoration: none;
    }
    .item:hover {
      border-color: #f90;
    }
    .item > a > img {
      width: 240px;
      height: 240px;
      border-width: 0;
    }
    .item > a > h3 { 
      margin: 0;
      padding: 0 0.5em;
      /*height: 44px;*/
      overflow: hidden;
      font: normal 14px/22px "微软雅黑";
    }
    .item > a > h3 > b {
       color: #393;
       border: 1px dotted #393;
    }
    .item > a > h3 > span {
      color: black;
    }
    .item > a > h3 > span:hover {
      color: #c00;
    }
    .item > a > div {
      margin: 0.5em 0.5em 0 0.5em;
      float: right;
      cursor: pointer;
      text-align: center;
      color: white;
      font: bolder 16px/30px "微软雅黑";
      padding: 0 1em;
      -moz-border-radius: 0.3em;
      -webkit-border-radius: 0.3em;
      border-radius: 0.3em;
      position: relative;
      behavior: url(/pie/PIE.htc);
    }
    .item > a > div > div {
      font: bolder 14px/18px "微软雅黑";
    }

    .item > div {
      margin: 0.5em;
      color: #c00;
      font: bold 16px/30px "微软雅黑"; 
    }
    .item > div > big {
      font: bold 30px/30px "微软雅黑";
    }
    .item > div > sup { 
       position: relative;
       left: -2.5em;
       top: -0.8em;
       color: #393;
       border: 1px dotted #393;
       font: bold 12px/14px "微软雅黑";
    }

    .item > div > div {
      margin: 0; 
      color: black;
      font: 14px/22px "微软雅黑"; 
    }

    .item > a > div.green {
      background-color: #393;
    }
    .item > a > div.red { 
      background-color: #e00;
    }
    .item > a > div.yellow { 
      color: #393;
      background-color: yellow;
    }
    .item > a > div.gray { 
      background-color: gray;
    }
    #header {
    }
    #nav {
      margin-top: 10px;
      color: #db4701;
      font: normal 16px/22px "微软雅黑";
      border-width: 0 0 2px 0;
      border-bottom: 2px solid #f27b03;
    }
    #nav > span {
      display:inline-block;
      *display:inline;
      *zoom:1;
      padding: 0 15px;
      margin-right: 3px;
      border-top: 1px solid white;
      border-left: 1px solid white;
      border-right: 1px solid white;
      border-width: 1px 1px 0 1px;
    }
    #nav > span:hover {
      border-top-color: #f90;
      border-left-color: #f90;
      border-right-color: #f90;
      background-color: #f6feed;
    }

    #nav > span.on {
      color: white;
      border-color: #f90;
      background-color: #f37c06;
    }
    .clearfix {
      clear: both;
    }
    

  </style>
</head>
<body>
  <div id="header">
    <a href="http://www.izheko/"><img src="/img/izheko-header.png" alt="爱折扣" /></a>
    <div id="nav">
      <span class="on">全部</span>
      <span>女装</span>
      <span>男装</span>
      <span>居家</span>
      <span>母婴</span>
      <span>鞋包</span>
      <span>配饰</span>
      <span>美食</span>
      <span>数码家电</span>
      <span>化妆品</span>
      <span>文体</span>
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
      <div title="这是折扣价哟。">
        ￥<big><?= $discount_price_yuan ?></big>.<?= $discount_price_fen ?>
        <?= $vip ? '<sup title="这是淘宝VIP用户价哟。">VIP价</sup>' : null ?>
        <?php if ($original_price > $discount_price) { ?>
        <div title="这是原价哟。">原价：<?= format_price($original_price) ?></div>
        <?php } ?>
      </div>
    </div>
<?php } ?>
  </div>
</body>
</html>
