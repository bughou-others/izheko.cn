        <div id="header">
            <div class="right">
<?php
    require APP_ROOT . "/view/component/footprint.view.php";
?>
                <span id="sns-share-span">
                    <div id="sns-share-a">分享</div>
<?php
    require APP_ROOT . "/view/component/sns_share.view.php";
?>
                </span>
            </div>
            <a href="/"><img class="logo" src="<?= App::static_server() ?>/tmp/t2.png" /></a>
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" /></div>
                <button type="submit">搜索</button>
            </form>
        </div>
