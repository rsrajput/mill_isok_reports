<?php
// config.php
$host = 'localhost';
$dbname = 'mill_tests';
$username = 'root';
$password = '';

ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'error.log'); // Log errors to error.log file


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>