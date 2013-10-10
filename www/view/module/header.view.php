        <div id="header">
            <div class="right">
                <script> Izheko.phone_init(); </script>
                <span id="sns-share-wrapper">
                    <div id="sns-share-button">分享<b></b></div>
                    <span id="sns-share" class="sns-share">
                        <i></i>
                    </span>
                </span>
                <script> Izheko.share_init(); </script>
                <span id="footprints-wrapper">
                    <div id="footprints-button">我的足迹<b></b></div>
                    <div id="footprints">
                        <i></i>
                        <div id="footprints-bar">
                            <span>清空</span>
                            <div>
                                <span>上 ↑</span>
                                <span>下 ↓</span>
                            </div>
                        </div>
                    </div>
                </span>
                <script> Izheko.Footprints.init(); </script>
            </div>
            <div id="logo-search">
                <a href="/" class="logo"></a>
                <form id="search" action="/search">
                    <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                    <button type="submit">搜 索</button>
                </form>
            </div>
            <?php require APP_ROOT . '/view/module/navbar.view.php'; ?>
        </div>
