<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:babangida.database.windows.net,1433; Database = baba", "ibrahimbabangida50", "{@Babrahim50}");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "ibrahimbabangida50", "pwd" => "{@Babrahim50}", "Database" => "baba", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:babangida.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
// Test insertion
try {
    $testPhone = "1234567890"; // Use a unique phone number for testing
    $testName = "mukamusoni";
    $testType = "Individual";
    $testClass = "Class 1";
    $testAmount = 100000;
    $testDistrict = "cyumbati";

    // Insert test data into Users table
    $stmt = $conn->prepare("INSERT INTO Users (phone, name, registration_type, contribution_class, monthly_amount, district) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$testPhone, $testName, $testType, $testClass, $testAmount, $testDistrict]);

    echo "Test data inserted successfully!<br>";
} catch (PDOException $e) {
    echo "Insertion failed: " . $e->getMessage();
}
?>
