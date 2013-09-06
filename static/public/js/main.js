function alert_time(run){
    var t = new Date().getTime();
    run();
    alert(new Date().getTime() - t + '    ' + i);
}
  function sleep(n)
  {
    var start = new Date().getTime();
    while(new Date().getTime() - start < n);
  }

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

function item_list_init(){
    if(navigator.userAgent.indexOf('MSIE 6.') > 0) {
        $(function(){
            //$('#item_list').css('height', $('#item_list').height() + 'px');
        });
    }
    $("#item_list").on('mouseenter', '.item-wrapper', function(){
        var $this = $(this);
        if(!$this.attr('x')){
            $this.children('.item').children('.expand').children('.end_time').after('<span class="sns-share">分享：<b class="sns-sina_weibo"></b><b class="sns-qq_weibo"></b><b class="sns-qzone"></b><b class="sns-renren"></b><b class="sns-douban"></b><b class="sns-kaixin"></b></span>');
            $this.attr('x', 'o');
        }
        $this.addClass('item-hover');
    }).on('mouseleave', '.item-wrapper', function(){
        $(this).removeClass('item-hover');
    });

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
function taobao_search(word){
    var w = $(window).width(), s;
    if(w > 638) s = '628x270';
    else if(w > 360) s = '350x270';
    else s = '290x380';
    document.write('<a data-type="2" data-keyword="' + word + '" data-rd="1" data-style="2" data-tmpl="' + s + '" target="_blank"></a>');
}

function in_viewport($c, $e){
    var ctop = $c.scrollTop() - 100;        /* 容器顶部 */
    var cbottom = ctop + $c.height() + 100; /* 容器底部 */
    var etop = $e.offset().top;             /* 元素顶部 */
    var ebottom = etop + $e.height();       /* 元素底部 */
    return etop < cbottom && ebottom > ctop;
}

        
