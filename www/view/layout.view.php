<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
　      <meta name="viewport" content="width=device-width, initial-scale=1" />
<?php
    require APP_ROOT . "/view/component/seo.view.php";
?>
        <link rel="shortcut icon" href="<?= App::static_server() ?>/img/favicon.ico" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server() ?>/main.css" />
        <script src="<?= App::static_server() ?>/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/main.js"></script>
    </head>
    <body>
<?php
    require APP_ROOT . "/view/component/header.view.php";
    require APP_ROOT . "/view/component/navbar.view.php";

    echo '<div id="content">';
    if (isset($target_view)) {
        require APP_ROOT . "/view/$target_view.view.php";
    }
    else echo '<img id="error_content" src="' . App::static_server() . '/img/404.png" />';
    echo '</div>';

    require APP_ROOT . "/view/component/footer.view.php";
?>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </body>
</html>
