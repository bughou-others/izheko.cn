
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
    var load_img = function(n){
            var item = $('#item' + n);
            if (item.length === 0) return;
            var title = item.children('h1');
            var img = $('<img/>').load(function(){
                $(this).parent().parent().css('background-image', 'none');
            });
            title.before(
                $('<a class="pic" data-itemid="' + numiid + '" href="#" target="_blank"></a>').prepend(img)
            ).css('margin-top', '0');
            var pic_url = item.attr('p');
            if (!pic_url) {
                var numiid = title.children('a:nth-child(2)').attr('data-itemid');
                pic_url = 'http://static.izheko.cn/pic/' + 
                    numiid.substr(0, 4).split('').join('/') + '/' + numiid + '.jpg';
            }
            img.attr('src', pic_url)
    };
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
            load_img(n);
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

