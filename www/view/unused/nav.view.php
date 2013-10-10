<?php
$cache = APP_ROOT . '/public/cache/nav.html';
if (is_file($cache))
    echo file_get_contents($cache);
else 
{
    ob_start();
?>
        <div id="navbar">
            <a href="/"<?= $type ? '' : ' class="on"' ?>>全部</a>
<?php
    require_once APP_ROOT . '/model/item.model.php';
    $types = Item::types();
    foreach($types as $one)
    {
        list($name, $pinyin, $count) = $one;
        $class = $pinyin === $type ? ' class="on"' : '';
        echo <<<EOL
            <a href="/$pinyin"$class>$name</a>\n
EOL;
    }
?>
        </div>
<?php
    if(!is_dir($dir = dirname($cache))) mkdir($dir, 0755, true);
    file_put_contents($cache, ob_get_flush());
}
?>
