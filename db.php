<?php
// Database connection details
$host = 'rmcdatabaseserver.database.windows.net';
$dbname = 'rmc';
$username = 'ibrahimbabangida50';
$password = '@Babrahim50';

// Path to the SSL certificate
$ssl_cert = __DIR__ . '/RMCF/BaltimoreCyberTrustRoot.crt.pem';

try {
    // Enable SSL in the connection options
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $ssl_cert,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Disable server certificate verification
    ];

    // Create a PDO instance with SSL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
