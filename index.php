<?php
require "vendor/autoload.php";
require "login.php";
require "scrap.php";

$loginUrl = 'http://alpineperfection.ddns.net/sessions/new';

try {
    $cookie = doLogin($loginUrl);
    doScrap($cookie);
} catch (Exception $e) {
    echo "Error occured: " . $e->getMessage() . "<br>";
}
