<?php
$servername = "mariadb";
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

echo $_SERVER["SERVER_SOFTWARE"] . "\n\n";
echo "Current PHP version: " . phpversion() . "\n\n";
echo "MariaDB connected successfully\n\n";
