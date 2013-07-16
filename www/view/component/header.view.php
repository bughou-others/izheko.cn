        <div id="header">
            <a href="/" class="logo"></a>
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                <button type="submit">搜　索</button>
            </form>
            <span id="footprints-span">
                <div id="footprints-a">我的足迹<b></b></div>
                <div id="footprints">
                    <div id="footprints-bar">
                        <span>清空</span>
                        <div>
                            <span>上 ↑</span>
                            <span>下 ↓</span>
                        </div>
                    </div>
                </div>
            </span>
            <script>
                Footprints.init();
            </script>
            <span id="welcome">
                <span>亲，欢迎您！</span>
                <span id="bookmark" title="亲，请按 Ctrl+D 哦"><b></b><span>Ctrl+D</span>收藏爱折扣</span>
                <script>
                    if(document.all) { //IE
                        $('#bookmark').attr('title', '').css('cursor', 'pointer').click(function(){
                            var url   = 'http://' + location.hostname +'/';
                            var title = '爱折扣 - 精选优质折扣商品';
                            window.external.AddFavorite(url, title);
                        });
                        $('#bookmark span').text('');
                    }
                </script>
            </span>
        </div>
