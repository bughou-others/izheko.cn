<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>爱折扣--管理系统</title>
    <style>
        body {
            margin: 0 0 2em 0;
            text-align: center;
            font-size: 16px;
            font-weight: normal;
        }
        #navigator {
            margin: 1em 0;
        }
        #navigator > a {
            color: white;
            text-decoration: none;
            background-color: gray;
            padding: 0.3em;
            border: 1px dotted gray;
            -moz-border-radius: 0.3em;
            -webkit-border-radius: 0.3em;
            border-radius: 0.3em;
            position: relative;
            behavior: url(/pie/PIE.htc);
        }
        #navigator > a.on {
            background-color: #393;
            border-color: #393;
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            margin: 0 auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0 0.5em; 
        }
        .pagination {
            margin: 1em 0;
            padding: 1em;
        }
        .pagination > a, .pagination > span {
            color: #666;
            text-decoration: none;
            margin: 0 0.2em;
            padding: 0.5em 0.8em;
        }
        .pagination > a, .pagination > span.current_page {
            border: 1px solid #ccc;
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=white,endColorstr=#f0f0f0);
            background-image: -moz-linear-gradient(top,#fff,#f0f0f0);
            background-image: -webkit-linear-gradient(top,#fff,#f0f0f0);
            background-image: -ms-linear-gradient(top,#fff,#f0f0f0);
        }
        .pagination > span.current_page {
            color: white;
            font-weight: bold;
            background: none repeat scroll 0 0 #FFA405;
            border: 1px solid #FE8101;
        }
    </style>
    <script src="/jquery-1.10.0.min.js"></script>
</head>
<body>
    <div id="navigator">
<?php
foreach(array(
    '/type_manage.do' => '爱折扣分类',
    '/category_manage.do' => '淘宝分类',
) as $uri => $name) {
    $class = $uri === $_SERVER['DOCUMENT_URI'] ? ' class="on"' : '';
    echo <<<EOT
        <a href="$uri"$class>$name</a>
EOT;
}
?>
    </div>
<?php
if (isset($target_view)) { 
    require APP_ROOT . "/view/$target_view.view.php";
} else {
?>
    <center style="margin-top: 10%;">爱折扣管理系统 欢迎您。</center>
<?php } ?>
</body>
</html>
