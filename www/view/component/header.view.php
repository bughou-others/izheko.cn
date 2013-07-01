        <div class="header">
            <a href="/" class="logo"></a>
            <form class="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                <div class="select-wrapper">
                    <div class="select-hide-border">
                        <select autocomplete="off" name="t">
<?php
$flag = isset($type) && isset($word) && strlen($word) > 0;
$selected = $flag && ($type === '' || $type === 'all') ? ' selected' : null
?>
                            <option value="all"<?= $selected ?>>全部</option>
<?php
    require_once APP_ROOT . '/model/item.model.php';
    $types = Item::types();
    foreach($types as $one)
    {
        list($name, $pinyin) = $one;
        $selected = $flag && $pinyin === $type ? ' selected' : '';
        echo <<<EOL
                            <option value="$pinyin"$selected>$name</option>\n
EOL;
    }
?>
                        </select>
                    </div>
                </div>
                <button type="submit">搜　索</button>
            </form>
        </div>
