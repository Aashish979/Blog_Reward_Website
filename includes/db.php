<?php
$host = 'localhost';
$dbname = 'blog_system';
$username = 'root'; // change if using different MySQL user
$password = '';     // change if password is set

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
