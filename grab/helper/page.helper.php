<?php
class Page
{
    function __construct($body, $url, $curl)
    {
        $this->url    = $url;
        $this->body   = $body;
        $this->curl   = $curl;
        $this->xpath  = null;
    }

    function query($xpath, $context = null, $charset = null)
    {
        if (!$this->xpath)
        {
            $doc = new DOMDocument();
            @$doc->LoadHTML(($charset ? 
                '<meta http-equiv="Content-Type" content="text/html; charset=' . $charset .'"/>'
                : '') . $this->body);
            $this->xpath = new DOMXPath($doc);
        }
        if ($context) return $this->xpath->query($xpath, $context);
        else          return $this->xpath->query($xpath);
    }

    function get($xpath, $context = null)
    {
        $href = $this->query($xpath, $context)->item(0);
        if ($href && ($url = $href->value))
        {
            return $this->curl->get($url, $this->url);
        }
    }

    function get_by_url($url)
    {
        return $this->curl->get($url, $this->url);
    }
}

