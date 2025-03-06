<?php
try {
    $conn = new PDO("sqlsrv:server = tcp:babangida.database.windows.net,1433; Database = baba", "ibrahimbabangida50", "@Babrahim50");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";

    // Test query
    $sql = "SELECT 1 AS test";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die();
}
?>
