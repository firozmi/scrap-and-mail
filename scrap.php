<?php
use PHPHtmlParser\Dom;

function doScrap($cookie = "")
{
    //send curl request along with cookie

    //open connection
    $ch = curl_init();

    $url = "http://alpineperfection.ddns.net/";
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Encoding:gzip, deflate, sdch',
        'Accept-Language:en-GB,en;q=0.8,en-US;q=0.6,ml;q=0.4',
        'Cache-Control:no-cache',
        'Connection:keep-alive',
        "Cookie:$cookie",
        'Host:alpineperfection.ddns.net',
        'Pragma:akamai-x-cache-on, akamai-x-cache-remote-on, akamai-x-check-cacheable, akamai-x-get-cache-key, akamai-x-get-extracted-values, akamai-x-get-ssl-client-session-id, akamai-x-get-true-cache-key, akamai-x-serial-no, akamai-x-get-request-id,akamai-x-get-nonces,akamai-x-get-client-ip,akamai-x-feo-trace',
        'Referer:http://alpineperfection.ddns.net/sessions/new',
        'Upgrade-Insecure-Requests:1',
    ));

    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $scrapData = curl_exec($ch);
    curl_close($ch);

    $scrapDom = new Dom;
    $scrapDom->load($scrapData);
    $values = $scrapDom->getElementsByClass("category_value")->toArray();
    $warmest = $values[2]->text();
    $warmest = substr($warmest, 0, 2);
    $coldest = $values[3]->text();
    $coldest = substr($coldest, 0, 2);
    $boiler = $values[6]->text();
    $subject = "HEAT H:$warmest L:$coldest BOILER $boiler";
    $message = $scrapDom->getElementById("home_page_left")->outerHtml();
    /* add email */
    $email = "";
    $from = "me@mine.com";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: ' . $from . "\r\n";
    $mailstatus = @mail(
        /* comma-separated list of recipients */
        "$email",

        /* message subject */
        "$subject",

        /* message contents */
        "$message",

        /* we must add sender email similar to this */
        "$headers"
    );
    if ($mailstatus == 1) {
        return true;
    } else {
        return false;
    }
}
