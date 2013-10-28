            <div  id="single-item" class="item">
                <h1><?= $item->type_tag() ?><a target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>"><?= $item->title() ?></a></h1>
                <span class="left">
                    <a class="pic" target="_blank" href="#" data-itemid="<?= $item->get('num_iid') ?>">
                        <!--[if IE 6]><span></span><![endif]-->
                        <img src="<?= $item->pic_url() ?>" />
                    </a>
                </span>
                <span class="right">
                    <?php if ($item->original_price_str()) { ?>
                        原价： <small><?= $item->original_price_str() ?>元</small>
                    <?php } ?>
                    <h2>
                        折扣价： <span><big><?= $item->discount_price_yuan() ?></big>.<?= $item->discount_price_fen() ?>元</span>
                        <?= $item->postage_tag() ?><?= $item->paigai_tag() ?><?= $item->vip_tag() ?> 
                    </h2>
                    <h3><?= $item->time_left() ?></h3>
                    <p><?= $item->get('ref_tip') ?></p>
                    <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"><?= $item->action() ?></a>
                    <a data-rd="2" class="remai" title="与这个宝贝相关的热卖宝贝" href="#" data-itemid="<?= $item->get('num_iid') ?>" target="_blank"></a>
                </span>
                <script> Izheko.single_item_init(); </script>
            </div>
