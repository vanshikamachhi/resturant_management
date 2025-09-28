<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default for XAMPP
$db   = 'restaurant';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
