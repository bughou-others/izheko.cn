<?php

class ErrorPage
{
    static function gen_404()
    {
        App::render(null);
    }
}

ErrorPage::gen_404();

