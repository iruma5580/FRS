<?php
// db_connect.php
// Reads from environment variables (Railway) with local fallbacks

$servername = getenv('MYSQLHOST')     ?: getenv('DB_HOST') ?: 'localhost';
$dbuser     = getenv('MYSQLUSER')     ?: getenv('DB_USER') ?: 'root';
$dbpass     = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
$dbname     = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'userdb';
$dbport     = (int)(getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306);

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname, $dbport);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");
?>
