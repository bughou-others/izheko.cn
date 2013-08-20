        <div id="sidebar">
            <div id="go_top"><a href="#"><span>回顶部</span><b></b></a></div>
            <script>
                $(window).scroll(function(){
                    $('#go_top').css('display', $(this).scrollTop() > 100 ? 'block' : 'none');
                }).scroll();
            </script>
<?php
if(isset($page) && $page >= 2) {
?>
            <div id="prev_page"><a href="<?= $page_url . ($page - 1) . '.html' ?>"><span>上一页</span><b></b></a></div>
<?php
} 
if(isset($data['total_count'], $page_size, $page) && ($data['total_count'] / $page_size) > $page) { 
?>
            <div id="next_page"><a href="<?= $page_url . ($page + 1) . '.html' ?>"><span>下一页</span><b></b></a></div>
<?php } ?>
            <div id="kefu"><a target="_blank" href="tencent://message/?uin=715091790"><span>客 服</span><b></b></a></div>
        </div>
        <script type="text/javascript">
            if(navigator.vendor === 'UCWEB') $("#sidebar").css('display', 'none');
        </script>
