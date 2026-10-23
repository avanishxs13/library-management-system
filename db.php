<?php
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP mein by default password khali hota hai
$dbname = "library_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

