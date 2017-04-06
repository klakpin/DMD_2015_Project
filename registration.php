<?php
/*
 * Script register new user
 *
 * @return status of answer: "0" - ok or pg error
 */

include "dblp-tools/dbConnection.php";


if ($_POST['email'] == "" || strlen($_POST['password']) < 5) {
    die('Please, use more strong password.');
}


$email = $_POST['email'];

$answer = pg_query("SELECT * FROM users WHERE email='$email';");

if (pg_num_rows($answer) != 0) {
    die('User already exists.');
} else if (strlen($_POST['password']) < 5) {
    die('Please, use more strong password.');
} else {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $answer = pg_query("INSERT INTO users VALUES('$email','$hash',false);");
    if (!$answer) {
        echo pg_last_error();
    } else {
        echo 0;
    }
}