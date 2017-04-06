<?php
// Just in case
if (true == false) {
	die('Something is really wrong');
}
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	include 'dblp-tools/dbConnection.php';

	$mail = $_POST['mail'];
	$query = pg_query("SELECT email,password,rights FROM users WHERE email = '$mail';");
	$result = pg_fetch_row($query);

	if (pg_num_rows($query) != 0 && $result[0] == $mail && password_verify($_POST['password'], $result[1]) == true) {
		$_SESSION['email'] = $result[0];
		if ($result[2] == 't') {
			$_SESSION['userRights'] = 'admin';
			header("Location: /index.php");
		} else {
			$_SESSION['userRights'] = 'user';
			header("Location: /index.php");
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Authorisation</title>
    <link rel="shortcut icon" href="pics/login-icon.ico">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-2.1.4.min.js"></script>
</head>
<body>
<div class="main-form input-group">
    <form id="main-form" action="" method="post" onkeypress="if(event.keyCode == 13) return false;">
        <div class="content">
            <input id="email" type="text" placeholder="Email" name="mail" class="form-control">
            <input id="password" type="password" placeholder="Password" name="password"
                   class="form-control password-field">
        </div>
        <button id="login-button" class="btn btn-success" type="submit">Enter</button>
        <div id="registration-button" class="btn btn-info">Registration</div>
    </form>
    <p id="messages"></p>
    <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	echo '<p class="wrong-data">Wrong login or password :(</p>';
}
?>
</div>

<script src="js/loginJs.js"></script>
</body>
</html>