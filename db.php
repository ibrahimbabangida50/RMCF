<?php
// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "ibrahimbabangida50", "pwd" => "@Babrahim50", "Database" => "baba", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:babangida.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
}
?>
