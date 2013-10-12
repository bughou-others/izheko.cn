
Izheko.phone_init = function() {
    if (Izheko.is_mobile) return;
    document.write('<span id="phone-edition" title="手机版爱折扣">手机版</span>');
    $('#phone-edition').click(function(){
        Izheko.Dialog.show('手机同样访问 www.izheko.cn 哟<br />爱折扣在所有设备上都表现完美！',
            $(this), 10);
    });
};

Izheko.share_init = function(){
    $('#sns-share').append(Izheko.SnsShareLib.icons_a).on('click', 'a', function(){
        Izheko.SnsShareLib.share(
            $(this).children('b'),
            'http://www.izheko.cn/',
            '我喜欢上了“爱折扣(www.izheko.cn)”每天9块9的小幸福。懂我的商品，懂我的价格，给力的9块9包邮。',
            'http://static.izheko.cn/img/logo.png'
            );
    });
    $('#sns-share-button').bind('click mouseenter', function(){
        Izheko.Dialog.hide();
        $('#sns-share').css('display', 'block');
        $('#sns-share-button').addClass('on');
    });
    $('#sns-share-wrapper').mouseleave(function(){
        $('#sns-share').css('display', 'none');
        $('#sns-share-button').removeClass('on');
    });
};

Izheko.Footprints = {
    init: function(){
        var o = this;
        $('#footprints-button').bind('click mouseenter', function(){
            o.show();
        });
        $('#footprints-wrapper').mouseleave(function(){
            $('#footprints').css('display', 'none');
            $('#footprints-button').removeClass('on');
        });
        $('#footprints-bar > span').click(function(){
            var time = new Date();
            time.setFullYear(time.getFullYear() - 1);
            document.cookie = 'footprints=; expires=' + time.toUTCString() + '; path=/';
            o.items = undefined;
            o.show('clear');
        });
        $('#footprints-bar > div > span:first-child').click(function(){
            o.show('prev');
        });
        $('#footprints-bar > div > span:last-child').click(function(){
            o.show('next');
        });
    },
    init_record: function(te) {
        var o = this;
        var s = '.title > a:nth-child(2), a.pic, a.action';
        (te || $('#item_list')).on('click mouseup contextmenu', s, function(e){
            if(e.type === 'mouseup' && e.which !== 2) return false;
            var item_id = $(this).closest('.item').find('a.pic').attr('data-itemid');
            var a = [ ];
            if(m = document.cookie.match(/(^| )footprints=(\d{8,}(,\d{8,})*)(;|$)/)) {
                a = m[2].split(',');
                if(a.length > 99) a = a.slice(0, 99);
                for(var i=0; i < a.length; ) {
                    if(a[i] === item_id) a.splice(i, 1);
                    else i++;
                }
            }
            a.unshift(item_id);
            var time = new Date();
            time.setFullYear(time.getFullYear() + 1);
            document.cookie = 'footprints=' + a.join(',') + '; expires=' + time.toUTCString() + '; path=/';
            //+ '; domain=' + location.hostname 
            o.items = undefined;
        });
    },
    page_size: 4,
    show: function(flag) {
        var o = this;
        o.flag = flag;
        if(o.items === undefined) {
            o.page = 0;
            var m;
            if(m = document.cookie.match(/(^| )footprints=(\d+(,\d+)*)(;|$)/)) {
                o.items = m[2].split(',');
            } else o.items = [ ];
        }
        if (o.items.length <= 0) { o.set(); return; }

        if(flag === 'next') o.page ++;
        else if(flag === 'prev') o.page --;
        var max_page = Math.ceil(o.items.length / o.page_size) - 1;
        if(o.page < 0) o.page = 0;
        else if(o.page > max_page) o.page = max_page;

        $('#footprints-bar > div > span:first-child').css('visibility', o.page > 0        ? 'visible' : 'hidden');
        $('#footprints-bar > div > span:last-child') .css('visibility', o.page < max_page ? 'visible' : 'hidden');

        var begin = o.page * o.page_size;
        var end   = begin + o.page_size;
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
                if(data.charAt(0) === '[') {
                    data = eval(data);
                    for(var i = 0; i < data.length; i++) {
                        var item = data[i];
                        o.items_data[item.num_iid] = item;
                    }
                }
                o.set(item_ids);
            }, 'text');
        }
        else o.set(item_ids);
    },
    set: function (item_ids) {
        var o = this;
        var html;
        var count = 0;
        if(item_ids) {
            html = '';
            var items_data = o.items_data;
            for(var i = 0; i < item_ids.length; i++) {
                var num_iid = item_ids[i];
                var item = items_data[num_iid];
                if(item === undefined) continue;
                count ++;
                html += '<div class="footprints-item"><a data-rd="1" class="image" target="_blank" data-itemid="' + num_iid +
                    '"><img src="' + item.pic_url + '" /></a><span class="desc"><a data-rd="1" target="_blank" data-itemid="' +
                    num_iid + '">' + item.title   + '</a><b>￥' + item.now_price + '</b></span></div>';
            }
        } else {
            html = '<center>亲，您还没有留下足迹哟。</center>';
        }
        var height = o.flag && o.last_count > count ? $('#footprints').height() + 'px' : 'auto';
        $('#footprints > .footprints-item, #footprints > center').remove();
        if(!o.flag && o.page === 0) {
            var wide = $(window).width() > 500;
            $('#footprints').css('width',       wide && count > 1 ? '464px' : '232px');
            $('#footprints-bar').css('width',   wide && count > 1 ? '424px' : '192px');
        }
        if(o.page === 0) {
            $('#footprints-bar').css('display', count > 0 ? 'block' : 'none');
        }
        Izheko.Dialog.hide();
        $('#footprints').css('height', height).prepend(html).css('display', 'block');
        $('#footprints-button').addClass('on');
        o.last_count = count;
    }
};

