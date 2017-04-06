<?php

//connection variables
$host = "localhost";
$port = "5433";
$user = "student";
$pass = "student";
$db = "testdb";

// Start of connection to database
$connection = pg_connect("host=$host port=$port dbname=$db user=$user password=$pass");
if (!$connection) {
	die("Could not open connection to database server");
}

$stat = pg_connection_status($connection);
if ($stat === PGSQL_CONNECTION_OK) {
} else {
	die("No connection");
}