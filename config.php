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

// Keep older databases compatible with tasks soft-deleted before the completed list replaced the bin.
$deletedAtColumn = $conn->query("SHOW COLUMNS FROM tasks LIKE 'deletedAt'");
if ($deletedAtColumn && $deletedAtColumn->num_rows === 0) {
    $conn->query(
        "ALTER TABLE tasks
        ADD COLUMN deletedAt DATETIME NULL DEFAULT NULL"
    );
}

// Permanently remove any legacy soft-deleted tasks after their 15-day retention period.
$conn->query(
    "DELETE FROM tasks
    WHERE deletedAt IS NOT NULL
      AND deletedAt <= DATE_SUB(NOW(), INTERVAL 15 DAY)"
);
?>
