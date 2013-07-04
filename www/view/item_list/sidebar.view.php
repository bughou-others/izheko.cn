        <div id="sidebar">
            <div id="go_top"><a href="#"><span>回顶部</span><b></b></a></div>
            <script>
                $(window).scroll(function(){
                    $('#go_top').css('display', $(this).scrollTop() > 100 ? 'block' : 'none');
                }).scroll();
            </script>
<?php if($page >= 2) { ?>
            <div id="prev_page"><a href="<?= $page_url . ($page - 1) . '.html' ?>"><span>上一页</span><b></b></a></div>
<?php } if(($data['total_count'] / $page_size) > $page) {  ?>
            <div id="next_page"><a href="<?= $page_url . ($page + 1) . '.html' ?>"><span>下一页</span><b></b></a></div>
<?php } ?>
        </div>
