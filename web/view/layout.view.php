<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="/static/main.css" />
    </head>
    <body>
        <div class="header">
            <a href="/"><img src="/static/logo.png" alt="爱折扣" /></a>
            <form class="search" action="/search">
                <div class="input_wrapper"><input type="text" name="s" /></div>
                <div class="submit_wrapper"><button type="submit">搜　索</button></div>
            </form>
        </div>
        <div class="nav">
            <a href="/"<?= isset($type) && !$type ? ' class="on"' : '' ?>>全部</a>
<?php
    require_once APP_ROOT . '/model/item.model.php';
    $types = Item::types();
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
            <p>爱折扣©2013 &nbsp; izheko.cn</p>
        </div>
    </body>
</html>
