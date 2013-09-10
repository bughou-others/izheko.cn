
function LazyImg(){
    var o = {
        img_index: 1,
        imgs: { },
        grids: { },
        complete: false,
        get_imgs: function(){
            if(document.getElementById('pagination')) this.complete = true;
            for(var e; e = document.getElementById('img' + this.img_index); this.img_index++){
                var $e = $(e);
                var id = $e.attr('id');
                if(!this.imgs[id]) this.imgs[id] = $e;
                this.add_to_grids(this.grids, $e);
            }
        },
        add_to_grids: function(grids, $e){
            var _top = $e.offset().top;
            var n_top    = Math.floor( _top                / 200);
            var n_bottom = Math.floor((_top + $e.height()) / 200);
            for(var n = n_top; n <= n_bottom; n++){
                if(grids[n])grids[n].push($e);
                else grids[n] = [ $e ];
            }
        },
        load_imgs: function(n_top, n_bottom){
            var row, $e, src;
            for(var n = n_top; n <= n_bottom; n++){
                if(row = this.grids[n]){
                    for(var i = 0; $e = row[i]; i++){
                        if(src = $e.attr('s')){
                            $e.attr('src', src).removeAttr('s');
                            delete this.imgs[$e.attr('id')];
                        }
                    }
                    delete this.grids[n];
                }
            }
        },
        init: function(){
            var o = this, $c = $(window);
            $c.scroll(function(){
                if(!o.complete) o.get_imgs();
                var _top = $c.scrollTop();
                var n_top    = Math.floor((_top               - 100) / 200);
                var n_bottom = Math.floor((_top + $c.height() + 100) / 200);

                o.load_imgs(n_top, n_bottom);
                if($.isEmptyObject(o.imgs)) $c.unbind('scroll resize');
            }).resize(function(){
                var tmp_grids = { };
                for(var id in o.imgs){
                    o.add_to_grids(tmp_grids, o.imgs[id]);
                }
                o.grids = tmp_grids;
                $c.scroll();
            });
        }
    };
    o.init();
    return o;
}
var Dialog = {
    show: function(msg, target, delay) {
        var o = this;
        if(o.box === undefined) {
            o.box = $('<div id="dialog_box"><b></b><div></div></div>').appendTo('body');
            o.box.children('b').click(o.hide);
        }
        o.box.children('div').html(msg);
        var pos = target.position();
        o.box.css({
            'left': pos.left + 'px',
            'top':  (pos.top + target.height() + 10) + 'px',
            'display': 'block'
        });
        if(o.timer !== undefined) clearTimeout(o.timer);
        o.timer = setTimeout(o.hide, (delay ? delay : 5) * 1000);
    },
    hide: function(){
        $(Dialog.box).css('display', 'none');
    }
};
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
        document.write('<span id="phone-edition" title="手机版爱折扣">手机版</span>');
        $('#phone-edition').click(function(){
            Dialog.show('手机同样访问 www.izheko.cn 哟<br />爱折扣在所有设备上都表现完美！',
                $(this), 10);
        });
        document.write('<span id="bookmark" title="收藏爱折扣">收藏</span>');
        $('#bookmark').click(function(){
            if (document.all) { //IE
                var url   = 'http://' + location.hostname +'/';
                var title = '爱折扣 - 精选优质折扣商品';
                window.external.AddFavorite(url, title);
            } else {
                Dialog.show('亲，请按 Ctrl+D 哦', $(this));
            }
        });
    }
};
var SnsShare = {
    init: function(){
        $('#sns-share').append(SnsShareLib.icons_a).on('click', 'a', function(){
            SnsShareLib.share(
                $(this).children('b'),
                'http://www.izheko.cn/',
                '我喜欢上了“爱折扣(www.izheko.cn)”每天9块9的小幸福。懂我的商品，懂我的价格，给力的9块9包邮。',
                'http://static.izheko.cn/img/logo.png'
                );
        });
        $('#sns-share-button').bind('click mouseenter', function(){
            Dialog.hide();
            $('#sns-share').css('display', 'block');
            $('#sns-share-button').addClass('on');
            $('#sns-share-button b').addClass('on');
        });
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
        Dialog.hide();
        $('#footprints').css('height', height).prepend(html).css('display', 'block');
        $('#footprints-button').addClass('on');
        $('#footprints-button b').addClass('on');
        o.last_count = count;
    }
};
var TimeLeftUpdate = {
    start: function(target){
        var time = target.attr('s');
        if(!time) return;
        time = parseInt(time, 10);
        var o = this;
        target.html(o.get_time_left(time));
        this.timer = setInterval(function(){
            target.html(o.get_time_left(time));
        }, 1000);
    },
    stop: function(){
        clearInterval(this.timer);
    },
    get_time_left: function(time){
        var left = time - Math.floor(new Date / 1000);
        var s = '', n;
        if((n = Math.floor(left / 86400)) > 0){
            s +=  n + '天';
            left = left % 86400;
        }
        n = Math.floor(left / 3600);
        s += (n < 10 ? '0' + n : n) + '小时';
        left = left % 3600;

        n = Math.floor(left / 60);
        s += (n < 10 ? '0' + n : n) + '分';
        left = left % 60;

        n = left;
        s += (n < 10 ? '0' + n : n) + '秒';
        return s;
    }
}
function item_list_init(){
    $("#item_list").on('mouseenter', '.item-wrapper', function(){
        var $this = $(this);
        var time_left = $this.children('.item').children('.expand').children('.time-left');
        if(!$this.attr('x')){
            time_left.after('<div class="sns-share">分享到：' + SnsShareLib.icons_b + '</div>');
            $this.attr('x', 'o');
        }
        TimeLeftUpdate.start(time_left.children('span'));
        $this.addClass('item-hover');
    }).on('mouseleave', '.item-wrapper', function(){
        TimeLeftUpdate.stop();
        $(this).removeClass('item-hover');
    }).on('click', '.sns-share b', item_sns_share);
    taodianjin_init();
    LazyImg();
    Footprints.init_record();
}
function single_item_init(){
    var item = $('#single-item');
    TimeLeftUpdate.start(item.children('.right').children('.time-left').children('span'));
    item.children('.left').children('.pic').after('<div class="sns-share">分享到：' + SnsShareLib.icons_b + '</div>');
    item.children('.left').children('.sns-share').on('click', 'b', item_sns_share);
    taodianjin_init();
}
function item_sns_share(){
    var $this = $(this);
    var item = $this.closest('.item');
    var img = item.find('.pic img');
    SnsShareLib.share($this, 
            'http://' + location.host,
            item.children('.title').text(),
            img.attr('s') || img.attr('src')
            );
}

function taodianjin_init(){
    (function(win,doc){
        var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
        if (!win.alimamatk_show) {
            s.charset = "gbk";
            s.async = true;
            s.src = "http://a.alimama.cn/tkapi.js";
            h.insertBefore(s, h.firstChild);
        };
        var o = { pid: "mm_40339139_4152163_13484640", rd: "1" };
        win.alimamatk_onload = win.alimamatk_onload || [];
        win.alimamatk_onload.push(o);
    })(window,document);
}
function taobao_search(word){
    var w = $(window).width(), s;
    if(w > 638) s = '628x270';
    else if(w > 360) s = '350x270';
    else s = '290x380';
    document.write('<a data-type="2" data-keyword="' + word + '" data-rd="1" data-style="2" data-tmpl="' + s + '" target="_blank"></a>');
}

var SnsShareLib = {
    sites: {
        sina_weibo: [ '新浪微博', function(url, title, pic){
            return "http://v.t.sina.com.cn/share/share.php?url=" + url + "&pic=" + pic + "&title=" + title;
        }],
        qq_weibo: [ '腾讯微博', function(url, title, pic){
            return "http://share.v.t.qq.com/index.php?c=share&a=index&url=" + url + "&pic=" + pic + "&title=" + title;
        }],
        qzone: [ 'QQ空间', function(url, title, pic){
            return "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" + url + "&pics=" + pic + "&title=" + title;
        }],
        renren: [ '人人网', function(url, title, pic){ 
            return "http://share.renren.com/share/buttonshare.do?link=" + url;
        }],
        douban: [ '豆瓣网', function(url, title, pic){
            return "http://www.douban.com/recommend/?url=" + url + "&title=" + title + "&image=" + pic;
        }],
        kaixin: [ '开心网', function(url, title, pic){
            return "http://www.kaixin001.com/rest/records.php?style=11&url=" + url + "&pic=" + pic + "&content=" + title;
        }]
    },
    gen_icons: function(a){
        var icons = '';
        var sites = this.sites;
        for(var key in sites){
            icons += (a ?
                    '<a><b class="sns-' + key + '"></b>' + sites[key][0] + '</a>'
                    :
                    '<b class="sns-' + key + '" title="' + sites[key][0] + '"></b>'
                    );
        }
        return icons;
    },
    share: function($e, url, title, pic){
        var url = this.sites[$e.attr('class').substr(4)][1](url, title, pic);
        if(!this.a){
            this.a = document.createElement('a');
            this.a.target = '_blank';
            document.body.appendChild(this.a);
        }
        this.a.href = url;
        this.a.click();
    },
    init: function(){
        this.icons_a = this.gen_icons(true);
        this.icons_b = this.gen_icons(false);
    }
};
SnsShareLib.init();

