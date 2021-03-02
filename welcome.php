<?php

session_start();

$message = null;

if(isset($_GET['logout'])) {
    session_unset();
    session_destroy();
}

if(isset($_SESSION['sessionid']) && $_SESSION['sessionid'] == session_id()) {
    $message = "<p>Je bent ingelogd en van harte welkom</p>";
} else {
    header("Location: index.php");
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome at the pleasure dome</title>
</head>
<body>
    <?=$message?>
    <a href="?logout">logout</a>
</body>
</html>
