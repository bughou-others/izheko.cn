        <div class="sidebar">
            <div class="go_top"><a href="#"><span>回到顶部</span><b></b></a></div>
<?php if($page >= 2) { ?>
            <div class="prev_page"><a href="<?= $page_url . ($page - 1) . '.html' ?>"><span>上一页</span><b></b></a></div>
<?php } if(($data['total_count'] / $page_size) > $page) {  ?>
            <div class="next_page"><a href="<?= $page_url . ($page + 1) . '.html' ?>"><span>下一页</span><b></b></a></div>
<?php } ?>
        </div>
