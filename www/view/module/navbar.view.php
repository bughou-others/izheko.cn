            <div id="navbar">
<?php
$class = isset($type) && (!$type || $type === 'all') ? ' class="on"' : '';
?>
                <a href="/"<?= $class ?>>首页</a>
<?php
require_once APP_ROOT . '/model/item.model.php';
foreach(ItemBase::$types as $tmp)
{
    list($name, $pinyin) = $tmp;
    $class = isset($type) && $pinyin === $type ? ' class="on"' : '';
    echo <<<EOL
                <a href="/$pinyin"$class>$name</a>\n
EOL;
}
?>
            </div>
