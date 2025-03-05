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
} catch (PDOException $e) {
    die("Error connecting to SQL Server: " . $e->getMessage());
}

// Handle Africa's Talking request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log the incoming request
    file_put_contents('request.log', print_r($_POST, true));

    // Get the request data
    $phone = $_POST['phoneNumber']; // Example field from Africa's Talking
    $text = $_POST['text']; // Example field from Africa's Talking

    // Save data to the database
    try {
        $stmt = $conn->prepare("INSERT INTO Users (phone, name) VALUES (:phone, :name)");
        $stmt->execute([
            ':phone' => $phone,
            ':name' => $text
        ]);

        echo "Data saved successfully!";
    } catch (PDOException $e) {
        die("Error saving data: " . $e->getMessage());
    }
} else {
    echo "Invalid request method.";
}
?>
