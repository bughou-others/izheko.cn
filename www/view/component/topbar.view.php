        <div class="topbar">
            <span class="left">亲，欢迎来到爱折扣！</span>
            <span class="right">
                <span id="my-history-span">
                    <a id="my-history-a" href="/my-history">浏览记录<b></b></a>
                    <div id="my-history">
                        <div class="my-history-item">
                            <a class="image"><img src="http://img04.taobaocdn.com/bao/uploaded/i4/19429035678996345/T1lR9GXtpiXXXXXXXX_!!0-item_pic.jpg_210x210.jpg" /></a>
                            <span class="desc">
                                <a>正品意大利STREP诗碧脱毛膏永久绝毛液脱毛膏 包邮</a>
                                <b>￥19.97</b>
                            </span>
                        </div>
                        <div id="my-history-bar">
                            <a href="/my-history">查看全部</a>
                            <span>清空</span>
                            <div>
                                <span>上 ↑</span>
                                <span>下 ↓</span>
                            </div>
                        </div>
                    </div>
                </span>
                <script>
function my_histroy(flag) {
    if(this.items_data === undefined) {
        this.items_data = { };
    }
    if(this.items === undefined) {
        var m;
        if(m = document.cookie.match(/(^| )my-history=(\d+(,\d+)*)(;|$)/)) {
            this.items = m[2].split(',');
        } else return null;
    }

    var page_size = 6;
    if(this.page === undefined) this.page = 0;
    if(flag === true) this.page ++;
    else if(flag === false) this.page --;
    if(this.page < 0) this.page = 0;
    else {
        var max_page = Match.ceil(this.items.length / page_size);
        if(this.page > max_page) this.page = max_page - 1;
    }
    var begin = this.page * page_size;
    var end   = begin + page_size;
    if(end > this.items.length) end = this.items.length;
    for(; begin < end; begin++) {
    }
}
                    $('#my-history-a').bind('click mouseenter', function(){
                        $('#my-history-span').addClass('my-history-span-on');
                        $('#my-history').css('display', 'block');
                    });
                    $('#my-history-span').mouseleave(function(){
                        $('#my-history').css('display', 'none');
                        $(this).removeClass('my-history-span-on');
                    });
                </script>
<!--
                <a href="javascript: void(0);">手机版</a>
                <a href="javascript: void(0);">android客户端</a>
                <a href="javascript: void(0);">iphone客户端</a>
-->
            </span>
        </div>
