<?php
header("Access-Control-Allow-Origin: *");

$qrCodeUrl = null;

if(isset($_GET['secret'])) {

// 2 factor authentication library
    require_once __DIR__ . '/vendor/autoload.php';
    $pga = new PHPGangsta_GoogleAuthenticator();

    $secret = $_GET['secret'];
    $website = 'http://localhost/two_factor_authentication/'; //Your Website
    $title = 'two_factor_authentication';
    $qrCodeUrl = $pga->getQRCodeGoogleUrl($title, $secret, $website);
}

echo $qrCodeUrl;

