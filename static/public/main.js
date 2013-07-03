var Footprints = {
    init: function(){
        var o = this;
        $('#footprints-a').bind('click mouseenter', function(){
            o.show();
        });
        $('#footprints-span').mouseleave(function(){
            $('#footprints').css('display', 'none');
            $(this).removeClass('footprints-span-on');
        });
        $('#footprints-bar > span').click(function(){
            var time = new Date();
            time.setFullYear(time.getFullYear - 1);
            document.cookie = 'footprints=; expires=' + time.toUTCString();
            o.items = undefined;
            o.show();
        });
        $('#footprints-bar > div > span:first-child').click(function(){
            o.show('prev');
        });
        $('#footprints-bar > div > span:last-child').click(function(){
            o.show('next');
        });
    },
    init_record: function() {
        var o = this;
        $('#item_list').on('click', '.item > .title > a, .item > a, .item > .buy > a', function(){
            var item_id = $(this).parents('.item').attr('item-id');
            var a = [ ];
            if(m = document.cookie.match(/(^| )footprints=(\d+(,\d+)*)(;|$)/)) {
                a = m[2].split(',');
                for(var i=0; i < a.length; ) {
                    if(a[i] === item_id) a.splice(i, 1);
                    else i++;
                }
            }
            a.unshift(item_id);
            var time = new Date();
            time.setFullYear(time.getFullYear + 1);
            document.cookie = 'footprints=' + a.join(',') + '; expires=' + time.toUTCString();
            //+ '; domain=' + location.hostname + '; path=/';
            o.items = undefined;
        });
    },
    show: function(flag) {
        var o = this;
        if(o.items === undefined) {
            o.page = 0;
            var m;
            if(m = document.cookie.match(/(^| )footprints=(\d+(,\d+)*)(;|$)/)) {
                o.items = m[2].split(',');
            }        
            else o.items = [ ];
        }
        if (o.items.length <= 0) {
            o.set();
            return;
        }

        var page_size = 4;
        if(flag === 'next') o.page ++;
        else if(flag === 'prev') o.page --;
        var max_page = Math.ceil(o.items.length / page_size) - 1;
        if(o.page < 0) o.page = 0;
        else if(o.page > max_page) o.page = max_page;

        $('#footprints-bar > div > span:first-child').css('visibility', o.page > 0        ? 'visible' : 'hidden');
        $('#footprints-bar > div > span:last-child') .css('visibility', o.page < max_page ? 'visible' : 'hidden');

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
            if(o.items_data[item_ids[i]] === undefined) to_fetch.push(item_ids[i]);
        }
        if(to_fetch.length > 0) {
            $.get('/footprints?item_ids=' + to_fetch.join(','), function(data){
                if(data[0] === '[') {
                    data = eval(data);
                    for(var i = 0; i < data.length; i++) {
                        var item = data[i];
                        o.items_data[item.id] = item;
                    }
                }
                o.set(item_ids);
            }, 'text');
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
                var item = items_data[id];
                if(item === undefined) continue;
                html += '<div class="footprints-item" item-id="' + id + 
                    '"><a class="image" href="' + item.jump_url + '"><img src="' + item.pic_url + 
                    '" /></a><span class="desc"><a href="' + item.jump_url + '">' + item.title + 
                    '</a><b>￥' + item.now_price + '</b></span></div>';
            }
        } else {
            html = '<center>亲，您还没有留下足迹哟。</center>';
        }
        $('#footprints > .footprints-item, #footprints > center').remove();
        if(this.page === 0) {
            var count = item_ids ? item_ids.length : 0;
            $('#footprints').css({
                'width':   count > 1 ? '464px' : '232px',
                'height':  count > 2 ? '252px' : count > 0 ? '140px' : 'auto'
            });
            $('#footprints-bar').css({
                'width':   count > 1 ? '424px' : '192px',
                'display': count > 0 ? 'block' : 'none'
            })
        }
        $('#footprints').prepend(html).css('display', 'block');
        $('#footprints-span').addClass('footprints-span-on');
    }
};
