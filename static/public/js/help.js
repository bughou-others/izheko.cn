(function(){
    var $window = $(window);
    var $wrapper = $('#help-wrapper');
    var $catalog = $('#help-catalog');
    var anchors  = $('.help-section a[name]');
    var cats = $('#help-catalog a[href^="#"]');
    var ie6 = navigator.userAgent.indexOf(' MSIE 6.') > 0;
    $window.bind('scroll resize', function(){
        if ($catalog.css('position') === 'static'){
            $catalog.css('margin-left', 0); return;
        }
        var wtop = $window.scrollTop();
        var _top = wtop - $wrapper.offset().top;
        if (_top < 0) {
            $catalog.css('margin-left', 0).removeClass('fixed bottom');
        } else if (ie6 || _top < $wrapper.innerHeight() - $catalog.innerHeight()) {
            $catalog.removeClass('bottom').css('margin-left',
                ie6 ? 0 : ($wrapper.offset().left + 'px')).addClass('fixed');
        } else {
            $catalog.css('margin-left', 0).removeClass('fixed').addClass('bottom');
        }
        var begin = $(anchors[0]).offset().top;
        for (var i = 1; i < anchors.length; i++) {
            var end = $(anchors[i]).offset().top;
            var pre = (end - begin) / 3;
            if (pre > 100) pre = 100;
            if (wtop < (end - pre)) break;
            begin = end;
        };
        var href = '#' + $(anchors[i - 1]).attr('name');
        cats.each(function(){
            if ($(this).attr('href') === href) $(this).addClass('on');
            else $(this).removeClass('on');
        });
    });
})();
