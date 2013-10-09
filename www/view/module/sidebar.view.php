        <div id="sidebar">
            <script>
                if (document.all) { //IE
                    document.write('<a id="bookmark2" title="收藏爱折扣"><span>收藏<br/>爱折扣</span></a><br/>');
                    $('#bookmark2').click(function(){
                        var url   = 'http://' + location.hostname +'/';
                        var title = '爱折扣 - 精选优质折扣商品';
                        window.external.AddFavorite(url, title);
                    });
                }
                $(window).scroll(function(){
                    $('#go_top').css('visibility', $(window).scrollTop() > 100 ? 'visible' : 'hidden');
                }).scroll();
            </script>
            <a id="kefu" href="tencent://message/?uin=715091790"><b></b><span>意见反馈</span></a><br/>
            <a id="go_top" href="#"><b></b><span>回到顶部</span></a>
            <script>
                $(window).scroll(function(){
                    $('#go_top').css('visibility', $(window).scrollTop() > 100 ? 'visible' : 'hidden');
                }).scroll();
            </script>
        </div>
