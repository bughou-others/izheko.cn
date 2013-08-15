<?php
class StaticPage
{
    static function run(){
        if (isset($_GET['target']) && $_GET['target'] === 'help')
            self::help_page();
        else self::static_page();
    }

    static function static_page()
    {
        App::render(null);
    }

    static function help_page()
    {
        App::render('module/help');
    }
}

StaticPage::run();

