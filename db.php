<?php
// Database connection details
$serverName = "tcp:rmcdatabaseserver.database.windows.net,1433";
$databaseName = "salama";
$username = "ibrahimbabangida50";
$password = "@Babrahim50";

// PDO Connection
try {
    $conn = new PDO(
        "sqlsrv:server=$serverName;Database=$databaseName;Encrypt=1;TrustServerCertificate=0",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully using PDO!";
} catch (PDOException $e) {
    die("Error connecting to SQL Server: " . $e->getMessage());
}
?>
