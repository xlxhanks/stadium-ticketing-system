<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$db   = 'stadium_ticketing';
$user = 'root';
$pass = 'Litusir1.';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
   // echo "Connection successful!";
}
?>