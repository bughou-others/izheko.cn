        <div id="header">
            <?php require APP_ROOT . "/view/component/footprint.view.php"; ?>
            <span id="sns-share-wrapper">
                <div id="sns-share-button">分享<b></b></div>
                <?php require APP_ROOT . "/view/component/sns_share.view.php"; ?>
            </span>
            <script>
                SnsShare.init();
                PhoneEdition_and_Bookmark.init();
            </script>
            <a href="/"><img class="logo" src="<?= App::static_server() ?>/tmp/t2.png" /></a>
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" /></div>
                <button type="submit">搜 索</button>
            </form>
<?php
    require APP_ROOT . "/view/component/navbar.view.php";
?>
        </div>
