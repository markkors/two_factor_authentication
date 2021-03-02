<?php
$login_error_message = null;

session_start();

// Database connection
require __DIR__ . '/config/db_connection.php';
$db = DB();

// Application library ( with DemoLib class )
require __DIR__ . '/library/library.php';
$app = new DemoLib($db);

// 2 factor authentication library
require_once __DIR__ . '/vendor/autoload.php';
$pga = new PHPGangsta_GoogleAuthenticator();


// check Login request
if (isset($_POST['submit'])) {
    if($userid = $app->Login($_POST['username'],$_POST['password'])) {
        $user = $app->UserDetails($userid);
        $checkResult = $pga->verifyCode($user->google_secret_code, $_POST['code'],1);
        if($checkResult) {
            $_SESSION['sessionid'] = session_id();
            header("Location: welcome.php");
        } else {
            session_unset();
            session_destroy();
            $login_error_message = "Error while logging in, authentication code incorrect";
        }

    } else {
        session_unset();
        session_destroy();
        $login_error_message = "Error while logging in, username / password incorrect";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-5 col-md-offset-3 well mx-auto" style="width: 50%">
            <h4>Login</h4>
            <?php
            if ($login_error_message != "") {
                echo '<div class="alert alert-danger"><strong>Error: </strong> ' . $login_error_message . '</div>';
            }
            ?>
            <form action="index.php" method="post">
                <div class="form-group">
                    <label for="">Username/Email</label>
                    <input type="text" name="username" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" name="password" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="">Auth code</label>
                    <input type="text" name="code" class="form-control"/>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-primary mt-3" value="Login"/>
                </div>
            </form>
            <div class="form-group">
                Nog geen account? <a href="registration.php">Registreer Hier</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>