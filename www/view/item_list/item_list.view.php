<?php
require_once APP_ROOT . '/view/item_list/filter.view.php';
if (empty($data['items'])) { ?>
        <div class="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
        <div class="item_list"><!--
<?php foreach($data['items'] as $item) { ?>
         --><div class="item">
                <div class="title">
                    <b><?= $item->type_tag() ?></b>
                    <a target="_blank" href="<?= $item->jump_url() ?>">
                        <?= $item->title() ?>
                    </a>
                </div>
                <a target="_blank" href="<?= $item->jump_url() ?>">
                    <img src="<?= $item->pic_url() ?>" />
                </a>
                <div class="buy">
                    <span title="折扣价 ￥<?= $item->discount_price_str() ?>">
                        ￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>
                    </span>
                    <?php if ($item->original_price_str()) { ?>
                    <small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small>
                    <?php } ?>
                    <a class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="<?= $item->jump_url() ?>" target="_blank">
                        <?= $item->action() ?>
                    </a>
                </div>
                <div class="flags"><?= $item->postage_tag() ?><?= $item->vip_tag() ?></div>
            </div><!--
<?php } ?>
     --></div>
        <div class="page">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    if($filter) $page_url .= $filter . '/';
    echo paginate($page_url, '.html', $page, $data['total_count'], $page_size);
?>
        </div>
<?php
    require_once APP_ROOT . '/view/item_list/sidebar.view.php';
}
?>