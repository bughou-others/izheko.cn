            <span id="sns-share">
                <i></i>
<?php
$title = '我喜欢上了“爱折扣(www.izheko.cn)”每天9块9的小幸福。懂我的商品，懂我的价格，给力的9块9包邮。';
$url   = 'http://www.izheko.cn/';
$pic   = 'http://static.izheko.cn/img/logo.png';

$sns_array = array(
    array(
        '新浪微博', 'sina_weibo',
        "http://v.t.sina.com.cn/share/share.php?url=$url&pic=$pic&title=$title"
    ),
    array(
        '腾讯微博', 'qq_weibo',
        "http://share.v.t.qq.com/index.php?c=share&a=index&url=$url&pic=$pic&title=$title"
    ),
    array(
        'QQ空间',   'qzone',
        "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=$url&pics=$pic&title=$title"
    ),
    array(
        '人人网',   'renren',
        "http://share.renren.com/share/buttonshare.do?link=$url"
    ),
    array(
        '豆瓣网',   'douban',
        "http://www.douban.com/recommend/?url=$url&title=$title&image=$pic"
    ),
    array(
        '开心网',   'kaixin',
        "http://www.kaixin001.com/rest/records.php?style=11&url=$url&pic=$pic&content=$title"
    ),
);

foreach($sns_array as $sns)
{
    list($name, $class, $url) = $sns;
    if (isset($no_text) && $no_text)
        echo <<<EOT
                <a target="_blank" class="$class" href="$url" title="分享到我的$name"><b></b></a>
EOT;
    else echo <<<EOT
                <a target="_blank" class="$class" href="$url" title="分享到我的$name"><b></b>$name</a>
EOT;
}
?>
            </span>
