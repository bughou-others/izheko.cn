
function in_viewport($c, $e){
    var ctop = $c.scrollTop() - 100;        /* 容器顶部 */
    var cbottom = ctop + $c.height() + 100; /* 容器底部 */
    var etop = $e.offset().top;             /* 元素顶部 */
    var ebottom = etop + $e.height();       /* 元素底部 */
    return etop < cbottom && ebottom > ctop;
}

        
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
