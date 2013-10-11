<!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="layoutmode" content="standard" />
        <meta name="viewport"   content="width=device-width,initial-scale=1.0" />
        <?php require 'module/seo.view.php'; ?>
        <link rel="shortcut icon" href="<?= App::static_server() ?>/img/favicon.ico?v=20130817" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/css/main.css?v=20131011.0" />
        <script src="<?= App::static_server() ?>/js/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/js/main.js?v=20131011.0"></script>
    </head>
    <body>
        <?php require 'module/header.view.php'; ?>
        <?php require 'module/sidebar.view.php'; ?>
        <div id="content">
            <script> Izheko.taodianjin_init(); </script>
            <?php
                if (isset($target_view)) require "content/$target_view.view.php";
                else echo '<img id="error_content" src="' . App::static_server() . '/img3/404.png" />';
            ?>
        </div>
        <?php require 'module/footer.view.php'; ?>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </body>
</html>
