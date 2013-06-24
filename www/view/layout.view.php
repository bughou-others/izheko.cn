<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>爱折扣-精选优质折扣商品</title>
        <meta name="keywords" content="爱折扣,九块九,九块九包邮,9.9包邮,优质折扣" />
        <meta name="description" content="爱折扣为您精选了淘宝、 天猫的优质折扣商品， 让您轻松找到物美价廉、 称心如意的宝贝。" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="/static/main.css" />
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21430461"></script>
    </head>
    <body>
        <div class="header">
            <a href="/" class="logo"></a>
            <form class="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                <div class="select-wrapper">
                    <div class="select-hide-border">
                        <select autocomplete="off" name="t">
<?php
$flag = isset($type) && isset($word) && strlen($word) > 0;
$selected = $flag && ($type === '' || $type === 'all') ? ' selected' : null
?>
                            <option value="all"<?= $selected ?>>全部</option>
<?php
    require_once APP_ROOT . '/model/item.model.php';
    $types = Item::types();
    foreach($types as $one)
    {
        list($name, $pinyin) = $one;
        $selected = $flag && $pinyin === $type ? ' selected' : '';
        echo <<<EOL
                            <option value="$pinyin"$selected>$name</option>\n
EOL;
    }
?>
                        </select>
                    </div>
                </div>
                <button type="submit">搜　索</button>
            </form>
        </div>
        <div class="nav">
<?php
    $class = isset($type) && (!$type || $type === 'all') ? ' class="on"' : '';
?>
            <a href="/"<?= $class ?>>首页</a>
<?php
    foreach($types as $one)
    {
        list($name, $pinyin, $count) = $one;
        $class = isset($type) && $pinyin === $type ? ' class="on"' : '';
        echo <<<EOL
            <a href="/$pinyin"$class>$name</a>\n
EOL;
    }
?>
        </div>
        <div class="filter">
            <a href="/">今日新品</a>
            <a href="/">即将开始</a>
            <a href="/">明日预告</a>
        </div>
        <div class="content">
<?php
if (isset($target_view)) {
    require APP_ROOT . "/view/$target_view.view.php";
}
else 
    echo '
            <img src="/static/404.png" class="error_content" />
    ';
?>
        </div>
        <div class="footer">
            <table>
                <tr>
                    <td rowspan="2"><img src="/static/logo.png" alt="爱折扣" /></td>
                    <th>爱折扣的价值：</th>
                    <td> 爱折扣为您精选了淘宝、 天猫的优质折扣商品， 让您轻松找到物美价廉、 称心如意的宝贝。</td>
                </tr>
                <tr>
                    <th>购买与支付：</th>
                    <td> 爱折扣仅提供到淘宝、天猫的链接， 不涉及任何交易行为， 所有购买与支付交易都在淘宝、天猫进行， 请放心购买。 </td>
                </tr>
            </table>
            <p>
                爱折扣©2013 &nbsp; izheko.cn
                &nbsp;&nbsp;&nbsp;&nbsp;
                <script src="http://s19.cnzz.com/stat.php?id=5452772&web_id=5452772&show=pic" language="JavaScript"></script>
            </p>
        </div>
    </body>
</html>
