<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leepos";



// $servername = "nathstack.tech";
// $username = "u500921674_leepos";
// $password = "OnGod@123";
// $dbname = "u500921674_leepos";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
