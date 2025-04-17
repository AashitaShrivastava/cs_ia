<?php
$host = "localhost"; // Change if using a remote server
$user = "root"; // Default user for XAMPP/MAMP
$password = ""; // Default is empty for XAMPP
$database = "virasat_db"; // Your database name

$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
$servername = "localhost";  // If using XAMPP or WAMP, "localhost" is correct.
$username = "root";         // Default username for XAMPP/WAMP is "root".
$password = "";             // Default password is empty.
$dbname = "virasat_db";    // Make sure this matches your database name.

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
<?php
$servername = "localhost";
$username = "root";  
$password = "";     
$database = "virasat_db"; 
$conn = new mysqli($servername, $username, $password, $database);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

