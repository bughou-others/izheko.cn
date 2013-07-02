var MyHistory = {
    init: function(){
        var o = this;
        $('#my-history-a').bind('click mouseenter', function(){
            $('#my-history-span').addClass('my-history-span-on');
            $('#my-history').css('display', 'block');
            o.show();
        });
        $('#my-history-span').mouseleave(function(){
            $('#my-history').css('display', 'none');
            $(this).removeClass('my-history-span-on');
        });
        $('#my-history-bar > span').click(function(){
            document.cookie = 'my-history=';
            o.items = undefined;
            o.show();
        });
        $('#my-history-bar > div > span:first-child').click(function(){
            o.show('prev');
        });
        $('#my-history-bar > div > span:last-child').click(function(){
            o.show('next');
        });
    },
    show: function(flag) {
        var o = this;
        if(o.items === undefined) {
            var m;
            if(m = document.cookie.match(/(^| )my-history=(\d+(,\d+)*)(;|$)/)) {
                o.items = m[2].split(',');
            } else {
                o.set();
                return;
            }
        }

        var page_size = 6;
        if(o.page === undefined) o.page = 0;
        if(flag === 'next') o.page ++;
        else if(flag === 'prev') o.page --;
        var max_page = Math.ceil(o.items.length / page_size) - 1;
        if(o.page < 0) o.page = 0;
        else if(o.page > max_page) o.page = max_page;

        //$('#my-history-bar > div > span:first-child').css('visibility', page > 0        ? 'visible' : 'hidden');
        //$('#my-history-bar > div > span:last-child').css('visibility', page < max_page ? 'visible' : 'hidden');

        var begin = o.page * page_size;
        var end   = begin + page_size;
        if(end > o.items.length) end = o.items.length;
        o.get(o.items.slice(begin, end));
    },
    get: function(item_ids) {
        var o = this;
        if(o.items_data === undefined) {
            o.items_data = { };
        }
        var to_fetch = [ ];
        for(var i = 0; i < item_ids.length; i++) {
            if(o.items_data[item_ids[i]] === undefined)
                to_fetch.push(item_ids[i]);
        }
        if(to_fetch.length > 0) {
            $.get('/my-history?item_ids=' + to_fetch.join(','), function(data){
                for(var i = 0; i < data.length; i++) {
                    var item = data[i];
                    o.items_data[item.id] = item;
                }
                o.set(item_ids);
            }, 'json');
        }
        else o.set(item_ids);
    },
    set: function (item_ids) {
        var html;
        if(item_ids) {
            html = '';
            var items_data = this.items_data;
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
    },
};
