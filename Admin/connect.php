<?php
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "hospital_management";

// Create connection
$conn = mysqli_connect($serverName, $userName, $password, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// else{
//     echo "success";
// }

