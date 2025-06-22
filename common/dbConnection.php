<?php
$username = "root";
$password = "";
$host = "localhost";
$dbname = "oromo_artisan_and_storyteller";

$con = new mysqli($host, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}