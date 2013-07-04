        <div id="navbar">
<?php
    $class = isset($type) && (!$type || $type === 'all') ? ' class="on"' : '';
?>
            <a href="/"<?= $class ?>>首页</a>
<?php
    foreach($types as $one)
    {
        list($name, $pinyin, $count) = $one;
        $class = isset($type) && $pinyin === $type ? ' class="on"' : '';
        echo <<<EOL
            <a href="/$pinyin"$class>$name</a>\n
EOL;
    }
?>
        </div>
