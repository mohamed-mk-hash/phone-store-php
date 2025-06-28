<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "coin_mobile";


$con = mysqli_connect($host, $username, $password, $dbname);


if (!$con) {
    die("connection failed". mysqli_connect_error());
}


?>