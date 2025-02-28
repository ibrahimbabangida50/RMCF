<?php
// Database connection
$host = 'babangida.mysql.database.azure.com';
$dbname = 'babangida';
$username = 'ibrahimbabangida50';
$password = '@Babrahim50';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
