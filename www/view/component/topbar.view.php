        <div id="topbar">
            <span class="left">
                亲，欢迎您！
                <span id="bookmark" title="亲，请按 Ctrl+D 哦"><b></b><span>Ctrl+D</span>收藏爱折扣</span>
                <script>
                    (function(){
                        var url   = 'http://' + location.hostname +'/';
                        var title = '爱折扣 - 精选优质折扣商品';
                        if(document.all) { //IE
                            $('#bookmark').attr('title', '').css('cursor', 'pointer').click(function(){
                                window.external.AddFavorite(url, title);
                            });
                            $('#bookmark span').text('');
                        } else if (window.sidebar && window.sidebar.addPanel) { //Firefox
                            $('#bookmark').attr('title', '').css('cursor', 'pointer').click(function(){
                                window.sidebar.addPanel(title, url, '');
                            });
                            $('#bookmark span').text('');
                        } else if(window.opera && window.print) { //Opera
                            $('#bookmark').attr('title', '').css('cursor', 'pointer').click(function(){
                                var a = document.createElement('a');
                                a.setAttribute('href', url);
                                a.setAttribute('title', title);
                                a.setAttribute('rel',  'sidebar');
                                a.click();
                            });
                            $('#bookmark span').text('');
                        }
                    })();
                </script>
            </span>
            <span class="right">
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
<!--
                <a href="javascript: void(0);">手机版</a>
                <a href="javascript: void(0);">android客户端</a>
                <a href="javascript: void(0);">iphone客户端</a>
-->
            </span>
        </div>
