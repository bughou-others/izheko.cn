<!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="layoutmode" content="standard" />
        <meta name="viewport"   content="width=device-width,initial-scale=1.0" />
<?php require 'module/seo.view.php'; ?>
        <link href="<?= App::static_server() ?>/img/favicon.ico?v=20130817" rel="shortcut icon" />
        <link href="<?= App::static_server() ?>/css/main.css?v=20131120.3" rel="stylesheet" />
        <script src="<?= App::static_server() ?>/js/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/js/main.js?v=20131120.1"></script>
    </head>
    <body>
<?php require 'module/header.view.php'; ?>
        <div id="sidebar">
            <script> Izheko.sidebar_init(); </script>
            <a id="gotop" href="#"></a>
            <script> Izheko.gotop_init(); Izheko.taodianjin_init(); </script>
        </div>
        <div id="content">
<?php if (isset($target_view)) require "content/$target_view.view.php"; else { ?>
            <img id="error_content" src="http://static.izheko.cn/img2/404.png"/>
<?php } ?>
        </div>
<?php require 'module/footer.view.php'; ?>
    </body>
</html>
