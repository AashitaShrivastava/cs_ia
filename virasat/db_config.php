<?php
$servername = "localhost";
$username = "root";
$password = "MySQL@Secure123!"; // Use the password you set earlier
$dbname = "ecommerce";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to MySQL!";
}
?>
