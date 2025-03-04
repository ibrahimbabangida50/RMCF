<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:rmcdatabaseserver.database.windows.net,1433; Database = salama", "ibrahimbabangida50", "{@Babrahim50}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "ibrahimbabangida50", "pwd" => "{@Babrahim50}", "Database" => "salama", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:rmcdatabaseserver.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>
