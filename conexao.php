<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "pharmafind";

$con = mysqli_connect($hostname, $username, $password, $database);

if (!$con) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>
