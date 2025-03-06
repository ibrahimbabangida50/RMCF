<?php
try {
    $conn = new PDO("sqlsrv:server = tcp:babangida.database.windows.net,1433; Database = baba", "ibrahimbabangida50", "{@Babrahim50}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
