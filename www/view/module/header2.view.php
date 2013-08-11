        <div id="header">
            <div class="right">
                <script> PhoneEdition_and_Bookmark.init(); </script>
                <span id="sns-share-wrapper">
                    <div id="sns-share-button">分享<b></b></div>
                    <?php require APP_ROOT . "/view/component/sns_share.view.php"; ?>
                </span>
                <script> SnsShare.init(); </script>
                <?php require APP_ROOT . "/view/component/footprint.view.php"; ?>
            </div>
            <div id="logo-search">
                <a href="/" class="logo"></a>
                <form id="search" action="/search">
                    <div class="input-wrapper"><input type="text" name="s" /></div>
                    <button type="submit">搜 索</button>
                </form>
            </div>
            <?php require APP_ROOT . "/view/component/navbar.view.php"; ?>
        </div>
