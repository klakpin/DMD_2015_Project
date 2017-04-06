<?php
if (strlen($_POST['password']) < 5) {
    die('Please, use more strong password.');
}
session_start();
include "dblp-tools/dbConnection.php";

if (isset($_SESSION['email'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_SESSION['email'];
    $answer = pg_query("UPDATE users SET password='$hash' WHERE email='$email'");
    if ($answer) {
        return 0;
    } else {
        return pg_last_error();
    }
}