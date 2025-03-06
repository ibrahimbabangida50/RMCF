<?php
// Get user input from the USSD gateway
$userInput = $_POST['text']; // Assuming the USSD gateway sends input via POST

try {
    // Connection details
    $serverName = "tcp:babangida.database.windows.net,1433";
    $databaseName = "baba";
    $username = "ibrahimbabangida50";
    $password = "@Babrahim50";

    // Establish connection
    $conn = new PDO("sqlsrv:server = $serverName; Database = $databaseName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query using user input
    $sql = "SELECT name FROM Users WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $response = "CON User found: " . $user['name'];
    } else {
        $response = "END User not found.";
    }
} catch (PDOException $e) {
    $response = "END Connection failed: " . $e->getMessage();
}

// Output the USSD response
header('Content-type: text/plain');
echo $response;
?>
