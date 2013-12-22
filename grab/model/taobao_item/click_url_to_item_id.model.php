<?php

require_once APP_ROOT . '/helper/curl.helper.php';
class ClickUrlToItemId
{
    static $curl;
    #click_url example(302 redirected to url2)
    #http://s.click.taobao.com/t?e=zGU34CA7K%2BPkqB07S4%2FK0CITy7klxn%2Fr3HZwuuY0VC7BwYarcZXIp9bMsJoojiel33mlHXMmbJHHucXDti114cIfXvSeMfG7BZmPu7U6%2BMtR29z3OHhr5kQv9YudEycyfvIEXsdUNhOEonURMZavJou%2Bgj9J1EHiypv5SIW8VrveWHUmCrg7A9q%2F9FfKMkca%2FleUkRo%3D&spm=2014.12057478.1.0&u=108kh5101010101010nmekf9101010T0 
    #url2 example(javascript jump to url3)
    #http://s.click.taobao.com/t_js?tu=http%3A%2F%2Fs.click.taobao.com%2Ft%3Fe%3DzGU34CA7K%252BPkqB07S4%252FK0CFcRfH0G7DLkP9xIxJLW2WdpnlmHlSOtQyCItqeryZPm2FQwFfM8puBXmT43I0RsdE%252BrcuWQsTgswUiAxeRQvmDJFFfh8P2YtU%252BN2jvESW6ThoFok87jMLIq8TQUnBTbPSVk%252BNkaDGFpEQi8XTDfKzQIJhs8dPyRq6Xf1vsVjJAyzEEA5I%253D%26spm%3D2014.12057478.1.0%26u%3D108kh5101010101010nmido0101010T0%26ref%3Dhttp%253A%252F%252Fju.jiukuaiyou.com%252Fjump%252F10wphy%26et%3DjFBC59HfJ7EkJg%253D%253D
    #url3 example(302 redirected to url4)
    #http://s.click.taobao.com/t?e=zGU34CA7K%2BPkqB07S4%2FK0CFcRfH0G7DLkP9xIxJLW2WdpnlmHlSOtQyCItqeryZPm2FQwFfM8puBXmT43I0RsdE%2BrcuWQsTgswUiAxeRQvmDJFFfh8P2YtU%2BN2jvESW6ThoFok87jMLIq8TQUnBTbPSVk%2BNkaDGFpEQi8XTDfKzQIJhs8dPyRq6Xf1vsVjJAyzEEA5I%3D&spm=2014.12057478.1.0&u=108kh5101010101010nmido0101010T0&ref=http%3A%2F%2Fju.jiukuaiyou.com%2Fjump%2F10wphy&et=jFBC59HfJ7EkJg%3D%3D
    #url4 example
    #http://detail.tmall.com/item.htm?id=18263930351&ali_trackid=2:mm_16674950_0_0,108kh5101010101010nmido0101010T0:1368018253_3k1_855976017&spm=2014.12057478.1.0

    static function fetch($click_url, $refer = null)
    {
        if (! $url2 = self::$curl->get_redirect_url($click_url, $refer))
        {
            error_log("no url2 from click_url: $click_url\n");
            return;
        }
        parse_str(parse_url($url2, PHP_URL_QUERY), $params);
        if (!isset($params['tu']) || (!$url3 = $params['tu']))
        {
            error_log("no url3 from url2: $url2\n");
            return;
        }
        if (! $url4 = self::$curl->get_redirect_url($url3, $url2))
        {
            error_log("no url4 from url3: $url3\n");
            return;
        }
        if (preg_match('@^http://s\.click\.tmall\.com/g\?@', $url4)) {
            parse_str(parse_url($url4, PHP_URL_QUERY), $params);
            if (isset($params['tar'])) $url4 = $params['tar'];
        }
        if (preg_match('@^http://redirect\.simba\.taobao\.com/rd\?@', $url4)) {
            parse_str(parse_url($url4, PHP_URL_QUERY), $params);
            if (isset($params['f'])) $url4 = $params['f'];
        }
        if (preg_match('@^http://re.taobao.com/@', $url4))
        {
            $page = self::$curl->get($url4);
            if (preg_match('/ data-item="(\d+)" /', $page->body, $m)) return $m[1];
            return;
        }
        parse_str(parse_url($url4, PHP_URL_QUERY), $params);
        if (isset($params['id']) && ($item_id = $params['id']))
        {
            if (($item_id = trim($item_id)) && preg_match('/^\d+$/', $item_id))
                return $item_id;
            else error_log("invalid item id $item_id from url4: $url4\n");
        }
        else
        {
            error_log("no item id from url4: $url4\n");
            return;
        }
    }
}
ClickUrlToItemId::$curl = new Curl();

