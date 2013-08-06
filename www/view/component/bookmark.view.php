            <span id="bookmark" title="亲，请按 Ctrl+D 哦"><b></b><span>Ctrl+D</span>收藏爱折扣</span>
            <script>
                if(document.all) { //IE
                    $('#bookmark').attr('title', '').css('cursor', 'pointer').click(function(){
                        var url   = 'http://' + location.hostname +'/';
                        var title = '爱折扣 - 精选优质折扣商品';
                        window.external.AddFavorite(url, title);
                    });
                    $('#bookmark span').text('');
                };
            </script>
