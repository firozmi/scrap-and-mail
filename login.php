<?php
use PHPHtmlParser\Dom;

//login and return cookie
function doLogin($loginUrl)
{
    $dom = new Dom;
    $dom->loadFromUrl($loginUrl);

    $html = $dom->outerHtml;

    $utf8 = "%E2%9C%93";
    $method = $dom->find('input[name="_method"]', 0)->getAttribute('value');
    $auth = $dom->find('input[name="authenticity_token"]', 0)->getAttribute('value');
    /* Add username and password */
    $login = "";
    $password = "";
    $remember = $dom->find('input[name="remember_me"]', 0)->getAttribute('value');
    $commit = $dom->find('input[name="commit"]', 0)->getAttribute('value');

    $fields = array(
        'utf8' => $utf8,
        '_method' => urlencode($method),
        'authenticity_token' => urlencode($auth),
        'login' => urlencode($login),
        'password' => urlencode($password),
        'commit' => urlencode($commit),
        'remember_me' => "1",
    );

    $fields_string = "";

    //url-ify the data for the POST
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }

    //open connection
    $ch = curl_init();

    $loginSubmit = "http://alpineperfection.ddns.net/sessions/login";
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $loginSubmit);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Origin' => 'http://alpineperfection.ddns.net',
        'Referer' => 'http://alpineperfection.ddns.net/sessions/new',
        'Upgrade-Insecure-Requests' => '1',
    ));

    //execute post
    $loggedIn = curl_exec($ch);
    curl_close($ch);

    $headers = get_headers_from_curl_response($loggedIn);

    return $headers["Set-Cookie"];
}

function get_headers_from_curl_response($response)
{
    $headers = array();

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i => $line) {
        if ($i === 0) {
            $headers['http_code'] = $line;
        } else {
            list($key, $value) = explode(': ', $line);

            $headers[$key] = $value;
        }
    }

    return $headers;
}
