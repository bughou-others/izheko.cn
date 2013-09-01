<?php
require_once APP_ROOT . '/view/item_list/filter.view.php';
if (empty($data['items'])) { ?>
        <div id="no_items">很抱歉，没有符合条件的宝贝。</div>
<?php } else { ?>
        <div id="item_list"><script> Footprints.init_record(); </script><!--
<?php foreach($data['items'] as $i => $item) { ?>
         --><div class="item-wrapper"><div class="item">
                <a class="pic" target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>">
                    <!--[if IE 6]><span></span><![endif]-->
<?php if ($i < 6) { ?>
                    <img src="<?= $item->pic_url() ?>" />
<?php } else { ?>
                    <img src="<?= App::static_server() ?>/img/tears.gif" data-original="<?= $item->pic_url() ?>" />
<?php } ?>
                </a>
                <div class="title">
                    <b><?= $item->type_tag() ?></b>
                    <a target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>">
                        <span><?= $item->title() ?></span>
                    </a>
                </div>
                <div class="buy">
                    <span class="price">
                        <span title="折扣价 ￥<?= $item->discount_price_str() ?>">
                            ￥<big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>
                        </span>
                        <?php if ($item->original_price_str()) { ?>
                        <small title="原价 ￥<?= $item->original_price_str() ?>">￥<?= $item->original_price_str() ?></small>
                        <?php } ?>
                    </span>
                    <?= $item->postage_tag() ?> <?= $item->vip_tag() ?>
                    <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank">
                        <b><?= $item->action() ?></b>
                    </a>
                </div>
                <div class="expand">
                    <span class="end_time"><?= $item->end_time() ?> 结束</span>
                </div>
            </div></div><!--
<?php } ?>
     --></div>
        <script src="<?= App::static_server() ?>/js/jquery.lazyload.min.js"></script>
        <script type="text/javascript">
            $(".item img[data-original]").lazyload({  threshold: 100 });
        </script>

        <div id="pagination">
<?php
    require_once APP_ROOT . '/../common/helper/page.helper.php';
    if($filter) $page_url .= $filter . '/';
    echo paginate($page_url, '.html', $page, $data['total_count'], $page_size);
?>
        </div>
<?php
}
?>
        <script type="text/javascript">
<?php if (isset($word) && $word !== '') { ?>
            var s, w = $(window).width();
            if(w > 638) s = '628x270';
            else if(w > 360) s = '350x270';
            else s = '290x380';
            document.write('<a data-type="2" data-keyword=<?= json_encode($word); ?> data-rd="1" data-style="2" data-tmpl="' + s + '" target="_blank"></a>');
<?php } ?>
            (function(win,doc){
                var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
                if (!win.alimamatk_show) {
                    s.charset = "gbk";
                    s.async = true;
                    s.src = "http://a.alimama.cn/tkapi.js";
                    h.insertBefore(s, h.firstChild);
                };
                var o = { pid: "mm_40339139_4152163_13484640", rd: "1" };
                win.alimamatk_onload = win.alimamatk_onload || [];
                win.alimamatk_onload.push(o);
            })(window,document);
        </script>
