<?php
// Database connection for Azure SQL Server
$host = 'tcp:babangida.database.windows.net,1433'; // Azure SQL Server host and port
$dbname = 'baba'; // Database name
$username = 'ibrahimbabangida50'; // Username
$password = '@Babrahim50'; // Password

try {
    // Use the sqlsrv driver for Azure SQL Server
    $pdo = new PDO("sqlsrv:server=$host;Database=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
