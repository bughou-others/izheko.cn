<?php
$title = '我喜欢上了“爱折扣(izheko.cn)”每天9块9的小幸福。懂我的商品，懂我的价格，给力的9块9包邮。真的要跟你八卦一下才行！';
$url   = 'http://www.izheko.cn/';
$pic   = 'http://static.izheko.cn/img/logo.png';

$sina_weibo = "http://v.t.sina.com.cn/share/share.php?url=$url&pic=$pic&title=$title";
$qq_weibo   = "http://share.v.t.qq.com/index.php?c=share&a=index&url=$url&pic=$pic&title=$title";
$douban     = "http://www.douban.com/recommend/?url=$url&title=$title&image=$pic";
$qzone      = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=$url&pics=$pic&title=$title";
$renren     = "http://share.renren.com/share/buttonshare.do?link=$url";
$kaixin     = "http://www.kaixin001.com/rest/records.php?style=11&url=$url&pic=$pic&content=$title";
?>
            <span id="sns_share">
                <a target="_blank" class="sina_weibo" href="<?= $sina_weibo ?>" title="分享到我的新浪微博"></a>
                <a target="_blank" class="qq_weibo"   href="<?= $qq_weibo   ?>" title="分享到我的腾讯微博"></a>
                <a target="_blank" class="qzone"      href="<?= $qzone      ?>" title="分享到我的QQ空间"></a>
                <a target="_blank" class="renren"     href="<?= $renren     ?>" title="分享到我的人人网"></a>
                <a target="_blank" class="douban"     href="<?= $douban     ?>" title="分享到我的豆瓣网"></a>
                <a target="_blank" class="kaixin"     href="<?= $kaixin     ?>" title="分享到我的开心网"></a>
            </span>
