<?php
$host = "localhost";
$dbname = "website_auth";
$dbuser = "root";
$dbpass = ""; // Change this if your MySQL account has a password.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit("Database connection failed. Check config.php and make sure MySQL is running.");
}
