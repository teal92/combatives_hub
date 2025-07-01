<?php
$host = "localhost";
$dbname = "combatives_hub";
$username = "root";
$password = "IQ2CuOF8R!iQ";
$charset = 'utf8mb4';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
