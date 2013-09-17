<?
function url_add_query_string($url, $query)
{
    if(($p = strpos($url, '#')) !== false)
    {
        $url = substr($url, 0, $p);
        $appendix = substr($url, $p);
    }
    if(strpos($url, '?') === false)
        $url .= '?' . $query;
    else 
        $url .= '&' . $query;
    if(isset($appendix)) $url .= $appendix;
    return $url;
}
