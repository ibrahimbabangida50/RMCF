<?php
try {
    // Connection details
    $serverName = "tcp:babangida.database.windows.net,1433";
    $databaseName = "baba";
    $username = "ibrahimbabangida50";
    $password = "@Babrahim50";

    // Establish connection
    $conn = new PDO("sqlsrv:server = $serverName; Database = $databaseName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";

    // Test query
    $sql = "SELECT 1 AS test";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Test query result: " . $result['test'];
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
