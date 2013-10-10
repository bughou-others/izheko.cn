<!doctype html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="layoutmode" content="standard" />
        <meta name="viewport"   content="width=device-width,initial-scale=1.0" />
<?php
    require APP_ROOT . "/view/component/seo.view.php";
    $suffix = preg_match('/^www([23]?)$/', App::sub_domain(), $m) ? $m[1] : '';
    if ($suffix === '2') {
        $icon_suffix    = '_red';
        $header_suffix  = '';
    }
    elseif ($suffix === '3') {
        $icon_suffix    = '_red';
        $header_suffix  = '3';
    } else {
        $icon_suffix    = '';
        $header_suffix  = '';
    }
?>
        <link rel="shortcut icon" href="<?= App::static_server() . "/img/favicon$icon_suffix.ico" ?>?v=20130817" />
        <link charset="utf-8" rel="stylesheet" type="text/css" href="<?= App::static_server(). "/css/main$suffix.css" ?>?v=20131010" />
        <script src="<?= App::static_server() ?>/js/jquery.min.js"></script>
        <script src="<?= App::static_server() ?>/js/main.js?v=20131009"></script>
    </head>
    <body>
        <?php require APP_ROOT . "/view/module/header$header_suffix.view.php"; ?>
        <div id="content">
            <?php
                if (isset($target_view)) require APP_ROOT . "/view/$target_view.view.php";
                else echo '<img id="error_content" src="' . App::static_server() . '/img3/404.png" />';
            ?>
        </div>
        <?php require APP_ROOT . '/view/module/sidebar.view.php'; ?>
        <?php require APP_ROOT . "/view/module/footer.view.php"; ?>
        <script src="http://l.tbcdn.cn/apps/top/x/sdk.js?appkey=21567955"></script>
    </body>
</html>
