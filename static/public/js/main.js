var PhoneEdition_and_Bookmark = {
    is_mobile: function(){ 
        var s = navigator.userAgent; 
        var a = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"); 
        var o;
        for (var i = 0; o = a[i]; i++) if (s.indexOf(o) > 0) return true; 
        return false; 
    },
    init: function(){
        if (this.is_mobile()) return;
        if (document.all || true) { //IE
            document.write('<span id="bookmark" title="收藏爱折扣">收藏</span>');
            $('#bookmark').click(function(){
                var url   = 'http://' + location.hostname +'/';
                var title = '爱折扣 - 精选优质折扣商品';
                window.external.AddFavorite(url, title);
            });
        };
        document.write('<span id="phone-edition" title="手机版爱折扣">手机版</span>');
    }
};
var SnsShare = {
    init: function(){
        $('#sns-share-button').bind('click mouseenter', function(){
            $('#sns-share').css('display', 'block');
            $('#sns-share-button').addClass('on');
            $('#sns-share-button b').addClass('on');
        });
        /*
           var ie6ie7 = navigator.userAgent.indexOf(' MSIE 6.0; ') > 0 ||
           navigator.userAgent.indexOf(' MSIE 7.0; ') > 0;
           */
        $('#sns-share-wrapper').mouseleave(function(){
            $('#sns-share').css('display', 'none');
            $('#sns-share-button').removeClass('on');
            $('#sns-share-button b').removeClass('on');
        });
    }
};
var Footprints = {
    init: function(){
        var o = this;
        $('#footprints-button').bind('click mouseenter', function(){
            o.show();
        });
        $('#footprints-wrapper').mouseleave(function(){
            $('#footprints').css('display', 'none');
            $('#footprints-button').removeClass('on');
            $('#footprints-button b').removeClass('on');
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
    init_record: function() {
        var o = this;
        var s = '.item > .title > a, .item > a, .item > .buy > a';
        $('#item_list').on('click mouseup contextmenu', s, function(e){
            if(e.type === 'mouseup' && e.which !== 2) return false;
            var item_id = $(this).parents('.item').children('.pic').attr('data-itemid');
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
            }        
            else o.items = [ ];
        }
        if (o.items.length <= 0) {
            o.set();
            return;
        }

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
                html += '<div class="footprints-item"><a class="image" data-itemid="' + num_iid +
                    '"><img src="' + item.pic_url + '" /></a><span class="desc"><a data-itemid="' +
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
        $('#footprints').css('height', height).prepend(html).css('display', 'block');
        $('#footprints-button').addClass('on');
        $('#footprints-button b').addClass('on');
        o.last_count = count;
    }
};
