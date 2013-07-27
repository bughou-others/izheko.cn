<?php
require_once APP_ROOT . '/view/item_list/filter.view.php';
if (empty($data['items'])) { ?>
        <div id="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
        <div id="item_list"><script> Footprints.init_record(); </script><!--
<?php foreach($data['items'] as $i => $item) { ?>
         --><div class="item" item-id="<?= $item->get('id') ?>">
                <div class="title">
                    <b><?= $item->type_tag() ?></b>
                    <a target="_blank" href="<?= $item->jump_url() ?>">
                        <span><?= $item->title() ?></span>
                    </a>
                </div>
                <a class="pic" target="_blank" href="<?= $item->jump_url() ?>">
                    <!--[if IE 6]><span></span><![endif]-->
<?php if ($i < 6) { ?>
                    <img src="<?= $item->pic_url() ?>" />
<?php } else { ?>
                    <img src="<?= App::static_server() ?>/img/tears.gif" data-original="<?= $item->pic_url() ?>" />
<?php } ?>
                </a>
                <div class="buy">
                    <span title="折扣价 ￥<?= $item->discount_price_str() ?>">
                        ￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>
                    </span>
                    <?php if ($item->original_price_str()) { ?>
                    <small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small>
                    <?php } ?>
                    <a class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="<?= $item->jump_url() ?>" target="_blank">
                        <b><?= $item->action() ?></b>
                    </a>
                </div>
                <div class="flags"><?= $item->postage_tag() ?><?= $item->vip_tag() ?></div>
            </div><!--
<?php } ?>
     --></div>
        <script src="<?= App::static_server() ?>/jquery.lazyload.js"></script>
        <script type="text/javascript">
            $(".item img[data-original]").lazyload({  threshold: 1000 });
            (function(win,doc){
                var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
                if (!win.alimamatk_show) {
                    s.charset = "gbk";
                    s.async = true;
                    s.src = "http://a.alimama.cn/tkapi.js";
                    h.insertBefore(s, h.firstChild);
                };
                var o = {
                    pid: "mm_40339139_4152163_13484640",/*推广单元ID，用于区分不同的推广渠道*/
                    appkey: "",/*通过TOP平台申请的appkey，设置后引导成交会关联appkey*/
                    unid: ""/*自定义统计字段*/
                };
                win.alimamatk_onload = win.alimamatk_onload || [];
                win.alimamatk_onload.push(o);
            })(window,document);
        </script>

        <div id="pagination">
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
