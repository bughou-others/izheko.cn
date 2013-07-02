        <div class="topbar">
            <span class="left">亲，欢迎来到爱折扣！</span>
            <span class="right">
                <span id="my-history-span">
                    <a id="my-history-a" href="/my-history">浏览记录<b></b></a>
                    <div id="my-history">
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
function my_history(flag) {
    if(flag === 'refresh') {
        this.items = undefined;
        return;
    }

    if(this.items === undefined) {
        var m;
        if(m = document.cookie.match(/(^| )my-history=(\d+(,\d+)*)(;|$)/)) {
            this.items = m[2].split(',');
        } else {
            set_my_history();
            return;
        }
    }

    var page_size = 6;
    if(this.page === undefined) this.page = 0;
    if(flag === 'down') this.page ++;
    else if(flag === 'up') this.page --;
    var max_page = Math.ceil(this.items.length / page_size) - 1;
    if(this.page < 0) this.page = 0;
    else if(this.page > max_page) this.page = max_page;
    
    //$('#my-history-bar > div > span:first-child').css('visibility', page > 0        ? 'visible' : 'hidden');
    //$('#my-history-bar > div > span:last-child').css('visibility', page < max_page ? 'visible' : 'hidden');

    var begin = this.page * page_size;
    var end   = begin + page_size;
    if(end > this.items.length) end = this.items.length;
    get_my_history(this.items.slice(begin, end));
}
function get_my_history(item_ids) {
    if(this.items_data === undefined) {
        this.items_data = { };
    }
    var to_fetch = [ ];
    for(var i = 0; i < item_ids.length; i++) {
        if(this.items_data[item_ids[i]] === undefined)
            to_fetch.push(item_ids[i]);
    }
    if(to_fetch.length > 0) {
        var items_data = this.items_data;
        $.get('/my-history?item_ids=' + to_fetch.join(','), function(data){
            for(var i = 0; i < data.length; i++) {
                var item = data[i];
                items_data[item.id] = item;
            }
            set_my_history(item_ids, items_data);
        }, 'json');
    }
    else set_my_history(item_ids, this.items_data);
}
function set_my_history(item_ids, items_data)
{
    var html;
    if(item_ids) {
        html = '';
        for(var i = 0; i < item_ids.length; i++) {
            var id   = item_ids[i];
            var item = items_data[id] || {  };
            html += '<div class="my-history-item" item-id="' + id + 
                '"><a class="image" href="' + item.jump_url + '"><img src="' + item.pic_url + 
                '" /></a><span class="desc"><a href="' + item.jump_url + '">' + item.title + 
                '</a><b>￥' + item.now_price + '</b></span></div>';
        }
        $('#my-history-bar').css('display', 'block');
    } else {
        html = '<center>亲，您还没有浏览过的宝贝哟。</center>';
        $('#my-history-bar').css('display', 'none');
    }
    $('#my-history > .my-history-item, #my-history > center').remove();
    $('#my-history').prepend(html);
}
                    $('#my-history-a').bind('click mouseenter', function(){
                        $('#my-history-span').addClass('my-history-span-on');
                        $('#my-history').css('display', 'block');
                        my_history();
                    });
                    $('#my-history-span').mouseleave(function(){
                        $('#my-history').css('display', 'none');
                        $(this).removeClass('my-history-span-on');
                    });
                    $('#my-history-bar > span').click(function(){
                        document.cookie = 'my-history=';
                        my_history('refresh');
                        my_history();
                    });
                    $('#my-history-bar > div > span:first-child').click(function(){
                        my_history('up');
                    });
                    $('#my-history-bar > div > span:last-child').click(function(){
                        my_history('down');
                    });
                </script>
<!--
                <a href="javascript: void(0);">手机版</a>
                <a href="javascript: void(0);">android客户端</a>
                <a href="javascript: void(0);">iphone客户端</a>
-->
            </span>
        </div>
