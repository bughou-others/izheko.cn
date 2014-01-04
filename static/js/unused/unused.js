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
