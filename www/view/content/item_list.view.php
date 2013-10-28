<?php
require_once 'filter.view.php';
if (empty($data['items'])) { ?>
            <div id="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
            <div id="item_list">
                <script> Izheko.item_count = <?= $item_count = count($data['items']) ?>; Izheko.item_list_init(); </script>
<?php foreach($data['items'] as $i => $item) { ?>
<div class="item-wrapper"><div class="item" id="item<?= ++$i ?>"><?php if ($i === 10 || $i === $item_count) { ?><script> Izheko.lazy_img(); </script><?php } ?>

    <h1><?= $item->type_tag() ?><a data-itemid="<?= $item->get('num_iid') ?>" href="#" target="_blank"><?= $item->title() ?></a></h1>
    <h2>
        <span title="折扣价 ￥<?= $item->discount_price_str() ?>">￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?></span><?php if ($item->original_price_str()) { ?> <small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small><?php } ?>

        <?= $item->postage_tag() ?><?= $item->paigai_tag() ?><?= $item->vip_tag() ?>

        <a data-rd="1" class="<?= $item->action_style() ?>" title="<?= $item->action_title() ?>" data-itemid="<?= $item->get('num_iid') ?>" href="#" target="_blank"><?= $item->action() ?></a>
    </h2>
    <div>
        <h3><?= $item->time_left() ?></h3>
        <p><?= $item->get('ref_tip') ?></p>
    </div>
</div></div>
<?php } ?>
            </div>
<?php
require_once APP_ROOT . '/../common/helper/page.helper.php';
if($filter) $page_url .= $filter . '/';
?>
            <div id="pagination"><?= paginate($page_url, '.html', $page, $data['total_count'], $page_size); ?></div>
<?php } ?>
<?php if (isset($word) && $word !== ''){ ?>
            <script> Izheko.taobao_search(<?=  json_encode($word) ?>); </script>
<?php } ?>
