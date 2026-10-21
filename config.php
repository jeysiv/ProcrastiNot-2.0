<?php
// 1. Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'procrastinot'; // Change this to your actual database name

// 2. Connect to MySQL using MySQLi
$conn = new mysqli($host, $username, $password, $database);

// 3. Check if the connection failed
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to utf8mb4 for full emoji/special character support
$conn->set_charset("utf8mb4");
?>