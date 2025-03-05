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

// Handle Africa's Talking request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request data
    $data = json_decode(file_get_contents('php://input'), true);

    // Example: Save data to the database
    try {
        $phone = $data['phoneNumber']; // Example field from Africa's Talking
        $text = $data['text']; // Example field from Africa's Talking

        // Insert data into the Users table
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
