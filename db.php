<?php
// db.php - DB connection for jobcompass.com
$host = 'localhost';
$db   = 'jobcompass';
$user = 'root';
$pass = ''; // set your password
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset($charset);
?>