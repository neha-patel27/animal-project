<?php
$host = "localhost";
$user = "root";    // default username for XAMPP
$pass = "";        // default password is blank
$dbname = "animal_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
