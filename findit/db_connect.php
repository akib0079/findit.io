<?php
// db_connect.php
$servername = "localhost";
$username = "root";       // Default XAMPP username
$password = "";           // Default XAMPP password is empty
$dbname = "lostandfound_db"; // Your database name from the SQL file

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>