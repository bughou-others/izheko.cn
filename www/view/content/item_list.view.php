<?php
require_once 'filter.view.php';
if (empty($data['items'])) { ?>
        <div id="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
        <div id="item_list"><script> Izheko.item_count = <?= count($data['items']) ?>; Izheko.item_list_init(); </script><!--
<?php foreach($data['items'] as $i => $item) { ?>
         --><div class="item-wrapper"><div class="item">
                <a class="pic" target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>">
                    <!--[if IE 6]><span></span><![endif]-->
<?php if ($i < 6) { ?>
                    <img src="<?= $item->pic_url() ?>" />
<?php } else { ?>
                    <?php if ($i === 6) { ?>
                    <script> Izheko.lazy_img(); </script>
                    <?php } ?>
                    <img id="img<?= $i + 1 ?>" src="<?= App::static_server() ?>/img3/tears.gif" s="<?= $item->pic_url() ?>" />
<?php } ?>
                </a>
                <div class="title"><?= $item->type_tag() ?><a target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>"><?= $item->title() ?></a></div>
                <div class="buy">
                    <span class="price">
                        <span title="折扣价 ￥<?= $item->discount_price_str() ?>">
                            ￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>
                        </span>
                        <?php if ($item->original_price_str()) { ?>
                        <small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small>
                        <?php } ?>
                    </span>
                    <?= $item->postage_tag() ?> <?= $item->vip_tag() ?> <?= $item->paigai_tag() ?>
                    <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"><?= $item->action() ?></a>
                </div>
                <div class="expand">
                    <div class="time-left"><?= $item->time_left() ?></div>
                    <div class="tip">小编： <span><?= $item->get('ref_tip') ?></span></div>
                </div>
            </div></div><!--
<?php } ?>
     --></div>

        <div id="pagination">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    if($filter) $page_url .= $filter . '/';
    echo paginate($page_url, '.html', $page, $data['total_count'], $page_size);
?>
        </div>
<?php
}
if (isset($word) && $word !== ''){
?>
        <script type="text/javascript">
            Izheko.taobao_search(<?=  json_encode($word) ?>);
        </script>
<?php
}
?>
