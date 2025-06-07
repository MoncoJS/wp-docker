<?php
$host = "db";
$user = "wordpress";
$pass = "wordpress";
$name = "wordpress";
$port = 3306;

// Create connection
$conn = new mysqli($host, $user, $pass, $name);

// Check connection
if ($conn->connect_error) {
    die("<div style='color: red'>Connection failed: " . $conn->connect_error . "</div>");
}
echo "<div style='color: green'>Connected successfully</div>";
?>

