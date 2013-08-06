<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>爱折扣 - 精选优质折扣商品</title>
        <style>
body {
    margin: 0;
    padding: 0;
}
            #header {
                background-color: #010101;
                padding: 10px;
                text-align: center;
                font-size: 12px;
                line-height: 18px;
                /*
                background: url(http://static.izheko.dev/tmp/color-line.png) left bottom repeat-x;
                */
            }
            #header img {
                vertical-align: middle;
            }
#search {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    font-size: 1em;
    margin-left: 3em;
    background-color: #84d516; 
    vertical-align: text-top;
    width: 15%;
    min-width: 10em;
    max-width: 15em;
    height: 31px;
    /*
    padding: 3px;
    *padding: 2px 3px 3px 3px;
    */
    overflow: hidden;
    white-space: nowrap;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    position: relative;
}
#search .input-wrapper {
    padding-right: 1em;
    margin-right: 40px;
    height: 31px;
}
#search input {
    width: 100%;
    margin: 0;
    border: none;
    padding: 7px;
    font-size: 1em;
    height: 17px;
    line-height: 17px;
    outline: none;
    background-color: white;
    -moz-border-radius: 4px 0 0 4px;
    -webkit-border-radius: 4px 0 0 4px;
    border-radius: 4px 0 0 4px;
}
#search button {
    position: absolute;
    right: 0;
    top: 5px;
    text-align: center;

    margin: 0;
    padding: 0;
    border: none;
    width: 40px;
    height: 22px;
    font-size: 14px;
    line-height: 22px;
    color: white;
    font-weight: bold;
    background-color: #84d516;
    cursor: pointer;
}
#footprints-span {
    float: right;
    position: relative;
    z-index: 999;
    text-align: left;
    white-space: normal;
}
#footprints-a {
    color: #c2c2c2;
    *display: inline;
    *zoom: 1;
    position: relative;
    z-index: 9999;
    padding: 5px 6px 2px 6px;
    border-left:  1px solid #f5f5f5;
    border-right: 1px solid #f5f5f5;
}
#footprints-a.on {
    border-color:  #ddd;
    border-bottom: 1px solid white;
    background-color: white;
}
#footprints-a b {
    display: inline-block;
    width: 0;
    height: 0;
    font-size: 0;
    line-height: 0;
    margin-left: 5px;
    border-style: solid;
    border-width: 4px;
    border-color: #666 #f5f5f5 #f5f5f5;
    vertical-align: -2px;
}
#footprints-a b.on {
    border-color: #fff #fff #666 #fff;
    vertical-align: 2px;
}


            #content {
                height: 100px;
                background-color: #eee;
                background: url(http://static.izheko.dev/tmp/bg.png) repeat 0 0;
            }
            #footer {
                height: 30px;
                background-color: #444;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <img src="t2.png" />
            <form id="search" action="/search">
                <div class="input-wrapper"><input type="text" name="s" /></div>
                <button type="submit">搜索</button>
            </form>
            <span>分享</span>
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
        </div>
        <div id="content">
        </div>
        <div id="footer">
        </div>

    </body>
</html>
