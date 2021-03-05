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
    $mail = $_POST['email'];
    $username = $_POST['username'];
    $secret = $pga->createSecret();
    $insertId=null;
    if($app->Register($_POST['name'],$mail,$username,$_POST['password'],$secret,$insertId)) {
        $website = 'http://rocmn.markkors.nl/two_factor_authentication/'; //Your Website
        $title= 'two_factor_authentication';
        $qrCodeUrl = $pga->getQRCodeGoogleUrl($title, $secret,$website);

        sendHTMLMail($username,$mail,$qrCodeUrl);

        $jscript = <<< CODE
<script>
document.getElementById("register").style.display='none';
async function fetchQRCode() {
    let qr = document.getElementById("qr");
    qr.style.display="flex";
    
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


function sendHTMLMail($user,$mail,$qr) {
    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $subject = "Registratie 2-factor authenticatie demo";
    $from = "mark@markkors.nl";

// Create email headers
    $headers .= 'From: '.$from."\r\n".
        'Reply-To: '.$from."\r\n" .
        'X-Mailer: PHP/' . phpversion();

// Compose a simple HTML email message
    $message = '<html><body>';
    $message .= '<h1 style="color:#f40;">Welkom ' . $user . '</h1>';
    $message .= '<p style="color:#080;font-size:18px;">Scan de volgende code in je authenticator app om toegang te krijgen tot de applicatie....</p>';
    $message .= sprintf('<img src="%s">',$qr);
    $message .= '</body></html>';

    mail($mail, $subject, $message, $headers);
}


function doMail($to) {
    $subject = 'the subject';
    $message = 'hello';
    $headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
<style>
    .registration_msg {
        display: none;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
</style>
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

<div class="registration_msg" id="qr">Scan de QR code in je e-mail met de Google / Microsoft Authenticator en gebruik deze om <a href="index.php"> in te loggen</a></div>
<?=$jscript?>

</body>
</html>

