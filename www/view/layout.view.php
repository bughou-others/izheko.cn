<!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="layoutmode" content="standard" />
        <meta name="viewport"   content="width=device-width,initial-scale=1.0" />
<?php
    require APP_ROOT . "/view/component/seo.view.php";
?>
        <link rel="shortcut icon" href="<?= App::static_server() ?>/img/favicon.ico" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/css/main2.css?v=20130806" />
        <script src="<?= App::static_server() ?>/js/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/js/main.js?v=20130803"></script>
    </head>
    <body>
<?php
    require APP_ROOT . "/view/module/header2.view.php";

    echo '
        <div id="content">
        ';

    if (isset($target_view)) {
        require APP_ROOT . "/view/$target_view.view.php";
    }
    else echo '<img id="error_content" src="' . App::static_server() . '/img/404.png" />';

    echo '
        </div>
        ';

    require APP_ROOT . "/view/module/footer.view.php";
?>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </body>
</html>
