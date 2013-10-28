var Izheko = { };

Izheko.is_mobile = (function() {
    var s = navigator.userAgent; 
    var a = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"); 
    var t;
    for (var i = 0; t = a[i]; i++) {
        if (s.indexOf(t) > 0) return true;
    }
    return false;
})();

Izheko.Dialog = {
    show: function(msg, target, delay) {
        if (this.box === undefined) {
            this.box = $('<div id="dialog_box"><b></b><div></div></div>').appendTo('body');
            this.box.children('b').click(this.hide);
        }
        this.box.children('div').html(msg);
        var offset = target.offset();
        this.box.css({
            'left': (offset.left - 150) + 'px',
            'top':  (offset.top + target.height() + 10) + 'px',
            'display': 'block'
        });
        if (this.timer !== undefined) clearTimeout(this.timer);
        this.timer = setTimeout(this.hide, (delay ? delay : 3) * 1000);
    },
    hide: function(){
        if (Izheko.Dialog.box) Izheko.Dialog.box.css('display', 'none');
    }
};

Izheko.SnsShareLib = {
    sites: {
        qq_zone: [ 'QQ空间', function(url, title, pic){
            return "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" + url + "&pics=" + pic + "&title=" + title;
        }],
        sina_weibo: [ '新浪微博', function(url, title, pic){
            return "http://v.t.sina.com.cn/share/share.php?url=" + url + "&pic=" + pic + "&title=" + title;
        }],
        tencent_weibo: [ '腾讯微博', function(url, title, pic){
            return "http://share.v.t.qq.com/index.php?c=share&a=index&url=" + url + "&pic=" + pic + "&title=" + title;
        }],
        renren: [ '人人网', function(url, title, pic){ 
            return "http://share.renren.com/share/buttonshare.do?link=" + url;
        }],
        douban: [ '豆瓣网', function(url, title, pic){
            return "http://www.douban.com/recommend/?url=" + url + "&title=" + title + "&image=" + pic;
        }],
        kaixin: [ '开心网', function(url, title, pic){
            return "http://www.kaixin001.com/rest/records.php?style=11&url=" + url + "&pic=" + pic + "&content=" + title;
        }],
        qq_haoyou: [ 'QQ好友', function(url, title, pic){
            return "http://connect.qq.com/widget/shareqq/index.html?url=" + url + "&pics=" + pic + "&desc=" + title;
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
        url = encodeURIComponent(url);
        pic = encodeURIComponent(pic);
        title = encodeURIComponent(title);
        var url = this.sites[$e.attr('class').substr(4)][1](url, title, pic);
        if(this.a === undefined){
            var a = document.createElement('a');
            if(a.click) {
                a.target = '_blank';
                document.body.appendChild(a);
                this.a = a;
            } else this.a = null;
        }
        if(this.a){
            this.a.href = url;
            this.a.click();
        } else location.href = url;
    },
    init: function(){
        this.icons_a = this.gen_icons(true);
        this.icons_b = this.gen_icons(false);
    }
};
Izheko.SnsShareLib.init();

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


Izheko.sidebar_init = function() {
    if (Izheko.is_mobile) return;
    document.write(
            '<div id="pindao-wrapper"><a id="pindao-button" href="#"></a></div>' +
            '<a id="bookmark" href="#"></a>' + 
            '<a id="fankui" href="tencent://message/?uin=715091790"></a>'
            );
    var pindao_inited;
    $('#pindao-button').bind('click mouseenter', function(){
        if (!pindao_inited) {
            $('<div><i></i></div>').append($('#navbar > a').clone()).appendTo('#pindao-wrapper');
            pindao_inited = true;
        }
        $('#pindao-wrapper > div').css('display', 'block');
    });
    $('#pindao-wrapper').mouseleave(function(){
        $('#pindao-wrapper > div').css('display', 'none');
    });
    $('#bookmark').click(function(){
        if (document.all) { //IE
            var url   = 'http://' + location.hostname +'/';
            var title = '爱折扣 - 精选优质折扣商品';
            window.external.AddFavorite(url, title);
        } else {
            Izheko.Dialog.show('亲，请按 Ctrl+D 哦', $(this));
        }
        return false;
    });
}

Izheko.gotop_init = function(){
    var $c = $(window);
    var gotop = $('#gotop');
    var state = true;
    $c.scroll(function(){
        if ($c.scrollTop() > 100 === state) return;
        state = !state;
        gotop.css('visibility', state ? 'visible' : 'hidden');
    }).scroll();
    gotop.click(function(){
        $c.scrollTop(0);
        return false;
    });
};

Izheko.taodianjin_init = function(){
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
};


Izheko.item_list_init = function() {
    $w = $(window);
    $("#item_list").on('mouseenter', '.item', function(){
        var item = $(this);
        var time_left = item.children('div').children('h3');
        if(!item.attr('x')){ time_left
        .prepend('<a data-itemid="' + item.children('h1').children('a:nth-child(2)').attr('data-itemid') + '" data-rd="2" class="remai" title="与这个宝贝相关的热卖宝贝" target="_blank"></a>')
        .next('p').prepend('<span>小编： </span>')
        .before('<h4 class="sns-share">分享：' + Izheko.SnsShareLib.icons_b + '</h4>');
            item.attr('x', 'o');
        }
        Izheko.TimeLeftUpdate.start(time_left.children('span'));
        item.addClass('item-hover' + ($w.width() < 678 ? ' auto-height' : ''));
    }).on('mouseleave', '.item', function(){
        Izheko.TimeLeftUpdate.stop();
        $(this).removeClass('item-hover auto-height');
    }).on('click', '.sns-share b', Izheko.item_sns_share);
    Izheko.Footprints.init_record();
};

Izheko.single_item_init = function() {
    var item = $('#single-item');
    Izheko.TimeLeftUpdate.start(item.children('.right').children('h3').children('span'));
    item.children('.left').children('.pic').after('<h4 class="sns-share">分享：' + Izheko.SnsShareLib.icons_b + '</h4>');
    item.children('.left').children('.sns-share').on('click', 'b', Izheko.item_sns_share);
    Izheko.Footprints.init_record(item);
};

Izheko.lazy_img = (function(){
    var first_row_top, row_size, loaded = { }, loaded_count = 0; $c = $(window);
    var load_imgs_in_viewport = function(){
        if (!first_row_top) return;
        var _top = $c.scrollTop() - first_row_top;
        var _bottom = _top + $c.height();
        var min = (Math.floor(_top   / 341)) * row_size + 1;
        if (min < 1) min = 1;
        var max = (Math.ceil(_bottom / 341) + 1) * row_size;
        if (max > Izheko.item_count) max = Izheko.item_count;
        //console.log(min, max);
        for (var n = min; n <= max; n++){
            if (loaded[n]) continue;
            var title = $('#item' + n).children('h1');
            if (title.length === 0) return;
            var numiid = title.children('a:nth-child(2)').attr('data-itemid');
            title.before(
                $('<a class="pic" data-itemid="' + numiid + '" href="#" target="_blank"></a>').prepend(
                    $('<img/>').load(function(){
                        $(this).parent().parent().css('background-image', 'none');
                    }).attr('src',
                        'http://static.izheko.cn/pic/' + numiid.substr(0, 4).split('').join('/') + '/' + numiid + '.jpg'
                    )
                )
            ).css('margin-top', '0');
            loaded[n] = true;
            loaded_count ++;
        }
        if (loaded_count >= Izheko.item_count) {
            $c.unbind('scroll', load_imgs_in_viewport).unbind('resize', init_row_model);
        }
    };
    var init_row_model = function(){
        var item = $('#item_list > .item:first');
        first_row_top = item.offset().top;
        row_size = 1;
        while (
            (item = item.next('.item')) &&
            item.offset().top === first_row_top
            ) row_size ++;
        first_row_top -= 12;
        load_imgs_in_viewport();
    }
    var timer;
    $c.scroll(function(){
        if (timer) clearTimeout(timer);
        setTimeout(load_imgs_in_viewport, 1000);
    }).resize(init_row_model);

    return init_row_model;
})();

Izheko.TimeLeftUpdate = {
    start: function(target){
        var time = target.attr('s');
        if(!time) return;
        time = parseInt(time, 10);
        var o = this;
        target.html(o.get_time_left(time));
        this.timer = setInterval(function(){
            target.html(o.get_time_left(time));
        }, 100);
    },
    stop: function(){
        clearInterval(this.timer);
    },
    get_time_left: function(time) {
        var left = time - new Date / 1000;
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

        n = left.toFixed(1);
        s += (n < 10 ? '0' + n : n) + '秒';
        return s;
    }
};

Izheko.item_sns_share = function(){
    var $this = $(this);
    var item = $this.closest('.item');
    var title = item.children('h1');
    Izheko.SnsShareLib.share($this, 
            'http://' + location.host + '/item/' + title.children('a:nth-child(2)').attr('data-itemid'),
            title.text(),
            item.find('.pic').children('img').attr('src')
            );
};

Izheko.taobao_search = function(word){
    var w = $(window).width(), s;
    if(w > 638) s = '628x270';
    else if(w > 360) s = '350x270';
    else s = '290x380';
    document.write('<a data-type="2" data-keyword="' + word + '" data-rd="1" data-style="2" data-tmpl="' + s + '" target="_blank"></a>');
}

