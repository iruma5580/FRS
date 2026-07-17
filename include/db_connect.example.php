<?php
// db_connect.php
// ============================================================
// Copy this file to include/db_connect.php and fill in your
// actual database credentials. Never commit db_connect.php!
// ============================================================

$servername = getenv('DB_HOST')     ?: "localhost";
$dbuser     = getenv('DB_USER')     ?: "your_db_username";
$dbpass     = getenv('DB_PASS')     ?: "your_db_password";
$dbname     = getenv('DB_NAME')     ?: "userdb";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");
?>
