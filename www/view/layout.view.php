<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>爱折扣-精选优质折扣商品</title>
        <meta name="keywords" content="爱折扣,九块九,九块九包邮,9.9包邮,优质折扣" />
        <meta name="description" content="爱折扣为您精选了淘宝、 天猫的优质折扣商品， 让您轻松找到物美价廉、 称心如意的宝贝。" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/main.css" />
        <script src="<?= App::static_server() ?>/jquery-1.10.0.min.js"></script>
        <script src="<?= App::static_server() ?>/main.js"></script>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21430461"></script>
    </head>
    <body>
<?php
    require APP_ROOT . "/view/component/topbar.view.php";
    require APP_ROOT . "/view/component/header.view.php";
    require APP_ROOT . "/view/component/navbar.view.php";

    echo '<div class="content">';
    if (isset($target_view)) {
        require APP_ROOT . "/view/$target_view.view.php";
    }
    else echo '<img src="' . App::static_server() . '/img/404.png" class="error_content" />';
    echo '</div>';

    require APP_ROOT . "/view/component/footer.view.php";
?>
    </body>
</html>
