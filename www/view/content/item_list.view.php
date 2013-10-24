<?php
require_once 'filter.view.php';
if (empty($data['items'])) { ?>
            <div id="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
            <div id="item_list">
                <script> Izheko.item_count = <?= $item_count = count($data['items']) ?>; Izheko.item_list_init(); </script>
<?php foreach($data['items'] as $i => $item) { ?>
<div class="item-wrapper"><div class="item">
    <a class="pic" id="pic<?= ++$i ?>" data-itemid="<?= $item->get('num_iid') ?>" target="_blank" href="#"></a><?php if ($i === 10 || $i === $item_count) { ?><script> Izheko.lazy_img(); </script><?php } ?>

    <div class="title"><?= $item->type_tag() ?><a href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"><?= $item->title() ?></a></div>
    <div class="buy">
        <span title="折扣价 ￥<?= $item->discount_price_str() ?>">￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?></span><?php if ($item->original_price_str()) { ?><small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small><?php } ?>

        <?= $item->postage_tag() ?><?= $item->paigai_tag() ?><?= $item->vip_tag() ?>

        <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"><?= $item->action() ?></a>
    </div>
    <div class="expand">
        <div class="time-left"><?= $item->time_left() ?></div>
        <div class="tip">小编： <span><?= $item->get('ref_tip') ?></span></div>
    </div>
</div></div>
<?php } ?>
            </div>
            <div id="pagination"><?php
require_once APP_ROOT . '/../common/helper/page.helper.php';
if($filter) $page_url .= $filter . '/';
echo paginate($page_url, '.html', $page, $data['total_count'], $page_size);
?></div>
<?php } ?>
<?php if (isset($word) && $word !== ''){ ?>
        <script> Izheko.taobao_search(<?=  json_encode($word) ?>); </script>
<?php } ?>
