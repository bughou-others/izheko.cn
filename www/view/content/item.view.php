            <div  id="single-item" class="item">
                <div class="title"><?= $item->type_tag() ?><a target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>"><?= $item->title() ?></a></div>
                <span class="left">
                    <a class="pic" target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>">
                        <!--[if IE 6]><span></span><![endif]-->
                        <img src="<?= $item->pic_url() ?>" />
                    </a>
                </span>
                <span class="right">
                    <?php if ($item->original_price_str()) { ?>
                    <div class="price">
                        原价： <small><?= $item->original_price_str() ?>元</small>
                    </div>
                    <?php } ?>
                    <span class="price">
                        折扣价： <span><big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>元</span>
                    </span>
                    <?= $item->postage_tag() ?> <?= $item->vip_tag() ?> <?= $item->paigai_tag() ?>
                    <div class="time-left"><?= $item->time_left() ?></div>
                    <div class="tip"><?= $item->get('ref_tip') ?></div>
                    <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"><?= $item->action() ?></a>
                    <a data-rd="2" class="remai" title="与这个宝贝相关的热卖宝贝" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"></a>
                </span>
                <script> Izheko.single_item_init(); </script>
            </div>
