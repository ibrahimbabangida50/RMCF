<?php
// Database connection details
$serverName = "tcp:rmcdatabaseserver.database.windows.net,1433";
$databaseName = "salama";
$username = "ibrahimbabangida50";
$password = "@Babrahim50";

// SQL Server Extension Connection
$connectionInfo = array(
    "UID" => $username,
    "PWD" => $password,
    "Database" => $databaseName,
    "LoginTimeout" => 30,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Connected successfully using sqlsrv_connect!";
}
?>
