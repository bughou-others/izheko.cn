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
