<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>爱折扣--管理系统</title>
    <style>
        html {
            height: 100%;
        }
        body {
            margin: 0;
            height: 100%;
            text-align: center;
            font-size: 16px;
        }
        #navigator {
            margin: 1em 0;
        }
        #navigator > a {
            color: white;
            text-decoration: none;
            background-color: gray;
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
            padding: 0 1em; 
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
