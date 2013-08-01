        <div id="header">
            <a href="/" class="logo"></a>
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" value="<?= isset($word) ? $word : null ?>" /></div>
                <button type="submit">搜　索</button>
            </form>
        </div>
