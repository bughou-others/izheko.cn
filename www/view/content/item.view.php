            <div  id="single-item" class="item" item-id="<?= $item->get('num_iid') ?>">
                <h1><?= $item->type_tag() ?><a target="_blank" href="<?= $item->url() ?>"><?= $item->title() ?></a></h1>
                <span class="left">
                    <a class="pic" target="_blank" href="<?= $item->url() ?>">
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
                    <a data-rd="1" class="action <?= $item->action_style() ?>" title="<?= $item->action_title() ?>" href="<?= $item->url() ?>" target="_blank"><?= $item->action() ?></a>
                </span>
                <script> Izheko.single_item_init(); </script>
            </div>
