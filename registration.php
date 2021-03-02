<?php

// Start Session
session_start();
$qrCodeUrl = null;
$jscript = null;

// Database connection
require __DIR__ . '/config/db_connection.php';
$db = DB();

// Application library ( with DemoLib class )
require __DIR__ . '/library/library.php';
$app = new DemoLib($db);

// 2 factor authentication library
require_once __DIR__ . '/vendor/autoload.php';
$pga = new PHPGangsta_GoogleAuthenticator();

$register_error_message = null;
// check Register request
if (isset($_POST['submit'])) {
    $secret = $pga->createSecret();
    $insertId=null;
    if($app->Register($_POST['name'],$_POST['email'],$_POST['username'],$_POST['password'],$secret,$insertId)) {

        $website = 'http://localhost/two_factor_authentication/'; //Your Website
        $title= 'two_factor_authentication';
        $qrCodeUrl = $pga->getQRCodeGoogleUrl($title, $secret,$website);
        $jscript = <<< CODE
<script>
document.getElementById("register").style.display='none';
async function fetchQRCode() {
    let response = await fetch('http://localhost/two_factor_authentication/qrcode.php?secret=$secret');
    let responseText = await response.text();
    let img=document.createElement("img");
    img.setAttribute("src",responseText);
    img.setAttribute("style","display:block;margin: 2% auto;width:50%;");
    let qr = document.getElementById("qr");
    qr.appendChild(img);
    qr.style.display="block";
    
}
(async() => {
    await fetchQRCode();
})();
 
</script>

CODE;


    } else {
        $register_error_message="Something went wrong while registering user...";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

</head>
<body>

<div class="container" id="register">
    <div class="row">
        <div class="col-md-5 col-md-offset-3 well mx-auto" style="width: 50%;">
            <h4>Register</h4>
            <?php
            if ($register_error_message != "") {
                echo '<div class="alert alert-danger"><strong>Error: </strong> ' . $register_error_message . '</div>';
            }
            ?>
            <form action="registration.php" method="post">
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="name" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="">Email</label>
                    <input type="email" name="email" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="">Username</label>
                    <input type="text" name="username" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" name="password" class="form-control"/>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-primary mt-3" value="Register"/>
                </div>
            </form>
            <div class="form-group">
                Klik hier om <a href="index.php">in te loggen</a> indien je al een account hebt.
            </div>
        </div>
    </div>
</div>

<div class="col-md-5 col-md-offset-3 well mx-auto" id="qr" style="display: none">Scan deze QR code met de Google / Microsoft Authenticator en gebruik deze om <a href="index.php">in te loggen</a></div>
<?=$jscript?>

</body>
</html>

