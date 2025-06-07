<?php
global $conn;

$host = "db";
$user = "wordpress";
$pass = "wordpress";
$name = "wordpress";

$conn = new mysqli($host, $user, $pass, $name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>

