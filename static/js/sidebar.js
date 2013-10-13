
Izheko.sidebar_init = function() {
    if (Izheko.is_mobile) return;
    document.write(
            //'<a id="pindao" href="#"></a>' +
            '<a id="bookmark" href="#"></a>' + 
            '<a id="fankui" href="tencent://message/?uin=715091790"></a>'
            );
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

